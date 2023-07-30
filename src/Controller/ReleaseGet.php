<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class ReleaseGet extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $release = $this->listTracks($request);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($release)
            ->renderResponse();
    }

    protected function listTracks($request) 
    {
        $releaseId = $request->getAttribute('releaseId');
        if (empty($releaseId) || !is_numeric($releaseId)) {
            throw new \InvalidArgumentException('Provide a release id, you cunt');
        }

        $data = $this->discogs->getRelease($releaseId);

        $release = [
            'artist' => [
                'name' => $data->artists[0]->name,
                'id' => $data->artists[0]->id
            ],
            'title' => $data->title,
            'thumb' => isset($data->images[0]) ? $data->images[0]->uri : null,
            'tracks' => []
        ];

        foreach ($data->tracklist as $t) {
            $release['tracks'][] = $t->title;
        }

        return $release;
    }
}
