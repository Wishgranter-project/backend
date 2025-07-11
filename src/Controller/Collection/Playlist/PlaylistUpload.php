<?php

namespace WishgranterProject\Backend\Controller\Collection\Playlist;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Controller\Collection\CollectionController;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\DescriptivePlaylist\Playlist;

/**
 * Adds playlists by upload it.
 *
 * @todo Refactor and document this. I don't remember where I was going with it.
 */
class PlaylistUpload extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $title    = (string) $request->post('title');
        if (empty($title)) {
            throw new \InvalidArgumentException('Inform a valid title for the playlist.');
        }

        $playlist = $this->playlistManager->createPlaylist($title, null, $title, $playlistId);
        $header   = $playlist->getHeader();

        try {
            $postData = $this->getPostData($request);
            foreach ($postData as $key => $v) {
                if ($header->isValidPropertyName($key)) {
                    $header->{$key} = $v;
                } else {
                    throw new \InvalidArgumentException('Unrecognized property ' . $key);
                }
            }
            $playlist->setHeader($header);
        } catch (\Exception $e) {
            $this->playlistManager->deletePlaylist($playlistId);
            throw $e;
        }

        $resource = new JsonResource();
        $data = $this->describer->describe($playlist);

        return $resource
            ->setStatusCode(201)
            ->addSuccess(201, 'Playlist created')
            ->setData($data)
            ->renderResponse();
    }

    protected function upload(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles['playlists'])) {
            throw new \InvalidArgumentException('Inform files');
        }

        $playlists = [];
        $files     = is_array($uploadedFiles['playlists'])
            ? $uploadedFiles['playlists']
            : [ $uploadedFiles['playlists'] ];

        $replaceExisting = $request->post('replace') ?? false;
        $errors = [];

        foreach ($files as $file) {
            if ($ar = $this->handleFile($file, $replaceExisting, $errors)) {
                $playlists = array_merge($playlists, $ar);
            }
        }

        $resource = new JsonResource();

        if ($playlists) {
            $data = [];
            foreach ($playlists as $p) {
                $data[] = $this->describer->describe($p);
            }

            $pData = count($data) > 1
                ? $data
                : $data[0];

            $resource
                ->setStatusCode(201)
                ->addSuccess(201, count($playlists) . ' file(s) uploaded.')
                ->setData($pData);
        } else {
            $resource
                ->setStatusCode(400)
                ->addError(400, 'No file uploaded.');
        }

        return $resource
            ->renderResponse();
    }

    protected function handleFile(
        UploadedFileInterface $file,
        bool $replaceExisting = false,
        array &$errors = []
    ): ?array {
        $extension = $this->getExtension($file->getClientFilename());

        switch ($extension) {
            case '.zip':
                return $this->handleZip($file, $replaceExisting, $errors);
                break;
            case '.dpls':
                return [ $this->handleSinglePlaylist($file, $replaceExisting, $errors) ];
                break;
            default:
                $errors[] = 'Could not upload ' . $file->getClientFilename() . ', invalid extension.';
                return null;
                break;
        }
    }

    protected function handleSinglePlaylist(
        UploadedFileInterface $file,
        bool $replaceExisting = false,
        array &$errors = []
    ): Playlist {
        $destiny = PLAYLISTS_DIR . $file->getClientFilename();

        if (file_exists($destiny) && $replaceExisting) {
            unlink($destiny);
        } elseif (file_exists($destiny)) {
            $base = basename($destiny, '.dpls');
            $destiny = PLAYLISTS_DIR . $this->playlistManager->getAvailableFilename($base) . '.dpls';
        }

        $file->moveTo($destiny);

        return new Playlist($destiny);
    }

    protected function handleZip(UploadedFileInterface $file, bool $replaceExisting = false, array &$errors = []): array
    {
        $zipFile = tempnam(sys_get_temp_dir(), 'zip');
        unlink($zipFile);
        register_shutdown_function('unlink', $zipFile);

        $file->moveTo($zipFile);

        $zip = new \ZipArchive();
        $zip->open($zipFile, \ZipArchive::RDONLY);

        $playlists = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);

            $basename = basename($stat['name']);
            if ($this->getExtension($basename) != '.dpls') {
                $errors[] = 'Could not upload ' . $stat['name'] . ', invalid extension.';
                continue;
            }

            $destiny = PLAYLISTS_DIR . $basename;
            if (file_exists($destiny) && $replaceExisting) {
                unlink($destiny);
            } elseif (file_exists($destiny)) {
                $base = basename($destiny, '.dpls');
                $destiny = PLAYLISTS_DIR . $this->playlistManager->getAvailableFilename($base) . '.dpls';
            }

            $zip->extractTo(PLAYLISTS_DIR, $stat['name']);

            $playlists[] = new Playlist($destiny);
        }

        return $playlists;
    }

    protected function getExtension(string $filename): string
    {
        return preg_match('#(\.[a-zA-Z]+)$#', $filename, $matches)
            ? strtolower($matches[1])
            : '';
    }
}
