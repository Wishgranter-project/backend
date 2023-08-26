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
        $info  = $this->searchArtists($request);
        $data  = $this->getArtists($info);
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

    protected function getArtists($info) 
    {
        $artists = [];

        foreach ($info->results as $r) {
            $artists[] = [
                'id' => $r->id,
                'name' => $r->title, 
                'thumb' => $r->thumb ?? null
            ];
        }

        return $artists;
    }

    protected function searchArtists(ServerRequestInterface $request) 
    {
        $name = $request->get('name');

        if (empty($name) || !is_string($name)) {
            throw new \InvalidArgumentException('Provide a search term, you lackwit');
        }

        return $this->discogs->searchForArtist($name);
    }
}
