<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class PlaylistCreate extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        return !empty($request->getUploadedFiles())
            ? $this->upload($request, $handler)
            : $this->fromScratch($request, $handler);
    }

    protected function fromScratch(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $title        = (string) $request->post('title');
        if (empty($title)) {
            throw new \InvalidArgumentException('Inform a title for the playlist');
        }

        $playlist     = $this->playlistManager->createPlaylist($title, null, $title, $playlistId);
        $header       = $playlist->getHeader();

        try {
            $postData = $this->getPostData($request);
            foreach ($postData as $k => $v) {
                if ($header->isValidPropertyName($k)) {
                    $header->{$k} = $v;
                } else {
                    throw new \InvalidArgumentException('Unrecognized property ' . $k);
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

    protected function upload(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {

    }
}