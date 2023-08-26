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
        $info  = $this->listReleases($request);
        $data  = $this->getReleases($info);
        $count = count($data);

        $resource = new JsonResource();
        return $resource
            ->setStatusCode(200)
            ->setData($data)
            ->setMeta('total', $info->pagination->items)
            ->setMeta('itensPerPage', $info->pagination->per_page)
            ->setMeta('pages', $info->pagination->pages)
            ->setMeta('page', $info->pagination->page)
            ->setMeta('count', $count)
            ->renderResponse();
    }

    protected function getReleases($info) 
    {
        $releases = [];

        foreach ($info->results as $r) {
            $releases[] = [
                'id' => $r->master_id,
                'name' => $r->title, 
                'thumb' => $r->thumb ?? null, 
                'year' => $r->year
            ];
        }

        return $releases;
    }

    protected function listReleases($request) 
    {
        $query = $request->get('artist');
        if (empty($query) || !is_string($query)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        $page = (int) $request->get('page', 1);

        return $this->discogs->getArtistAlbums($query, $page);
    }
}
