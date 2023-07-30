<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class DiscoverArtists extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $data   = $this->listArtists($request);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->renderResponse();
    }

    protected function listArtists(ServerRequestInterface $request) 
    {
        $name = $request->get('name');

        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        $data = $this->discogs->searchForArtist($name);

        $artists = [];
        foreach ($data->results as $r) {
            $artists[] = [
                'id' => $r->id,
                'name' => $r->title, 
                'thumb' => $r->thumb ?? null
            ];
        }

        return $artists;
    }
}
