<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;

use AdinanCenci\DescriptivePlaylist\Playlist;

use AdinanCenci\Player\Helper\JsonResource;

class PlaylistCreateUpload extends ControllerBase 
{
    public function __construct() 
    {
        parent::__construct();
        $this->resource = new JsonResource();
    }

    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles['playlists'])) {
            throw new \InvalidArgumentException('Inform files');
        }

        $playlists = [];
        $files     = is_array($uploadedFiles['playlists'])
            ? $uploadedFiles['playlists']
            : [ $uploadedFiles['playlists'] ];

        foreach ($files as $file) {
            if ($ar = $this->handleFile($file)) {
                $playlists = array_merge($playlists, $ar);
            }
        }

        if ($playlists) {
            $data = [];
            foreach ($playlists as $p) {
                $data[] = $this->describer->describe($p);
            }

            $this->resource
                ->setStatusCode(201)
                ->addSuccess(201, count($playlists) . ' file(s) uploaded.')
                ->setData($data);
        } else {
            $this->resource
                ->setStatusCode(400)
                ->addError(400, 'No file uploaded.');
        }

        return $this->resource->renderResponse();
    }

    protected function handleFile(UploadedFileInterface $file) : ?array
    {
        $extension = $this->getExtension($file->getClientFilename());

        switch ($extension) {
            case '.zip':
                return $this->handleZip($file);
                break;
            case '.dpls':
                return [ $this->handlePlaylist($file) ]; 
                break;
            default:
                return null;
                break;
        }
    }

    protected function handlePlaylist(UploadedFileInterface $file) : Playlist
    {
        $destiny = PLAYLISTS_DIR . $file->getClientFilename();

        if (file_exists($destiny)) {
            unlink($destiny);
        }

        $file->moveTo($destiny);

        return new Playlist($destiny);
    }

    protected function handleZip(UploadedFileInterface $file) : array 
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
                continue;
            }

            $destiny = PLAYLISTS_DIR . $basename;
            if (file_exists($destiny)) {
                unlink($destiny);
            }

            $zip->extractTo(PLAYLISTS_DIR, $stat['name']);

            $playlists[] = new Playlist($destiny);
        }

        return $playlists;
    }

    protected function getExtension(string $filename) : string
    {
        return preg_match('#(\.[a-zA-Z]+)$#', $filename, $matches) 
            ? strtolower($matches[1])
            : '';
    }
}
