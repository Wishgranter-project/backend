<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\Player\Helper\JsonResource;

class DiscoverSources extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $sources = $this->find($request);

        $resource = new JsonResource();
        return $resource
            ->setData($sources)
            ->renderResponse();

        return $response;
    }

    protected function find($request) 
    {
        $query   = $this->getQuery($request);
        $ids     = $this->youtubeSearcher->search($query);
        $sources = [];

        foreach ($ids as $id) {
            $sources[] = [
                'service' => 'youtube',
                'id' => $id
            ];
        }

        return $sources;
    }

    protected function getQuery($request) : string
    {
        $query = $request->get('title');

        if ($request->get('artist')) {
            $query .= ' ' . $request->get('artist');
        }

        if ($request->get('soundtrack')) {
            $query .= ' ' . $request->get('soundtrack');
        }

        return $query;
    }
}
