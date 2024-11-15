<?php

namespace WishgranterProject\Backend\Discover\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\AetherMusic\Aether;
use WishgranterProject\AetherMusic\Description;
use WishgranterProject\AetherMusic\Search\SearchResults;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\Describer;

class DiscoverResources extends ControllerBase
{
    /**
     * @var WishgranterProject\AetherMusic\Aether
     *   The aether service.
     */
    protected Aether $aether;

    /**
     * The describer service.
     *
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param WishgranterProject\AetherMusic\Aether $aether
     *   The aether service.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
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
        $description = $this->buildDescription($request);
        $search      = $this->aether->search($description);
        $search->addDefaultCriteria();

        $searchResults = $search->find();

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

    protected function buildDescription(ServerRequestInterface $request): Description
    {
        $title      = $request->get('title');
        $artist     = $request->get('artist');
        $soundtrack = $request->get('soundtrack');
        $genre      = $request->get('genre');

        if (!$title) {
            throw new \InvalidArgumentException('Inform a valid title');
        }

        return Description::createFromArray([
            'title'      => $title,
            'artist'     => $artist,
            'soundtrack' => $soundtrack,
            'genre'      => $genre,
        ]);
    }
}
