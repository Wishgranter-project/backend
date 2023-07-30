<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class DiscoverReleases extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $releases = $this->listReleases($request);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($releases)
            ->renderResponse();
    }

    protected function listReleases($request) 
    {
        $query = $request->get('artist');
        if (empty($query) || !is_string($query)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        $page = (int) $request->get('page', 0);

        $data = $this->discogs->getArtistAlbums($query, $page);

        $releases = [];
        foreach ($data->results as $r) {
            $releases[] = [
                'id' => $r->master_id,
                'name' => $r->title, 
                'thumb' => $r->thumb ?? null
            ];
        }

        return $releases;
    }
}
