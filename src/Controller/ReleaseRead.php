<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class ReleaseRead extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $release = $this->getRelease($request);

        $data = $this->describer->describe($release);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }

    protected function getRelease(ServerRequestInterface $request) 
    {
        $releaseId = $request->getAttribute('releaseId');
        return $this->discographyDiscogs->getAlbumById($releaseId);
    }
}
