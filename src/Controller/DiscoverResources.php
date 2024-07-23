<?php

namespace WishgranterProject\Backend\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\AetherMusic\Aether;
use WishgranterProject\AetherMusic\Description;
use WishgranterProject\AetherMusic\Search\SearchResults;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\Backend\Helper\JsonResource;

class DiscoverResources extends ControllerBase
{
    /**
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\AetherMusic\Aether $aether
     * @param WishgranterProject\Backend\Service\Describer $describer
     */
    public function __construct(Aether $aether, Describer $describer)
    {
        $this->aether    = $aether;
        $this->describer = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self(
            $servicesManager->get('aether'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $searchResults = $this->find($request);

        $data = [];
        foreach ($searchResults as $item) {
            $data[] = $this->describer->describe($item->resource);
        }

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();

        return $response;
    }

    protected function find(ServerRequestInterface $request): SearchResults
    {
        $description = $this->buildDescription($request);
        $search      = $this->aether->search($description);

        $search->addDefaultCriteria();

        $searchResults = $search->find();
        return $searchResults;
    }

    protected function buildDescription(ServerRequestInterface $request): Description
    {
        $title      = $request->get('title');
        $artist     = $request->get('artist');
        $soundtrack = $request->get('soundtrack');

        if (!$title) {
            throw new \InvalidArgumentException('Inform a valid title');
        }

        return Description::createFromArray([
            'title'      => $title,
            'artist'     => $artist,
            'soundtrack' => $soundtrack,
        ]);
    }
}
