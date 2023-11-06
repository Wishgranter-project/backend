<?php 
namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use AdinanCenci\AetherMusic\Description;

use AdinanCenci\Player\Helper\JsonResource;

use AdinanCenci\AetherMusic\Sorting\LikenessTally;
use AdinanCenci\AetherMusic\Sorting\CriteriaInterface;
use AdinanCenci\AetherMusic\Sorting\TitleCriteria;
use AdinanCenci\AetherMusic\Sorting\SoundtrackCriteria;
use AdinanCenci\AetherMusic\Sorting\ArtistCriteria;
use AdinanCenci\AetherMusic\Sorting\UndesirablesCriteria;
use AdinanCenci\AetherMusic\Sorting\LeftOverCriteria;

class DiscoverResources extends ControllerBase 
{
    public function formResponse(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $resources = $this->find($request);

        $data = [];
        foreach ($resources as $resource) {
            $data[] = $this->describer->describe($resource);
        }

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();

        return $response;
    }

    protected function find(ServerRequestInterface $request) : array
    {
        $description = $this->buildDescription($request);
        $search      = $this->aether->search($description);

        $undesirables = [
            'cover'    => -1,
            'acoustic' => -1,
            'live'     => -20,
            'demotape' => -1,
            'demo'     => -1,
            'remixed'  => -1,
            'remix'    => -1,
        ];

        if ($description->cover) {
            unset($undesirables['cover']);
        }

        $search
        ->addCriteria(new TitleCriteria(10))
        ->addCriteria(new ArtistCriteria(10))
        ->addCriteria(new SoundtrackCriteria(10))
        ->addCriteria(new UndesirablesCriteria(1, $undesirables))
        ->addCriteria(new LeftoverCriteria(1));

        $resources = $search->find();
        return $resources;
    }

    protected function buildDescription(ServerRequestInterface $request) : Description
    {
        return Description::createFromArray([
            'title'      => $request->get('title'),
            'artist'     => $request->get('artist'),
            'soundtrack' => $request->get('soundtrack')
        ]);
    }
}
