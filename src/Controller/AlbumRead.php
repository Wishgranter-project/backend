<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class AlbumRead extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $album = $this->getRelease($request);

        $data = $this->describer->describe($album);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }

    protected function getRelease(ServerRequestInterface $request) 
    {
        $artistName = $request->get('artist');
        $title = $request->get('title');
        return $this->discographyMusicBrainz->getAlbum($artistName, $title);
    }
}
