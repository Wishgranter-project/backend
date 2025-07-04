<?php

namespace WishgranterProject\Backend\Controller\Discover;

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

/**
 * Given the description of a music, searches for playable media.
 */
class DiscoverResources extends ControllerBase
{
    /**
     * The aether service.
     *
     * @var WishgranterProject\AetherMusic\Aether
     */
    protected Aether $aether;

    /**
     * The describer service.
     *
     * @var WishgranterProject\Backend\Service\Describer
     */
    protected Describer $describer;

    /**
     * Constructor.
     *
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
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $description = $this->buildDescription($request);
        $search      = $this->aether->search($description);
        $search->addDefaultCriteria();

        $searchResults = $search->find();

        $data = [];
        foreach ($searchResults as $item) {
            $data[] = $this->describer->describe($item->resource);
        }

        $debug = [
            'description' => $this->describer->describe($description)
        ];

        return $this->jsonResource($data)
            ->setMeta('debug', $debug)
            ->renderResponse();
    }

    /**
     * Builds a music description from the request's properties.
     *
     * @param Psr\Http\Message\ServerRequestInterface $request
     *   The HTTP request object.
     *
     * @return WishgranterProject\AetherMusic\Description
     *   The music description.
     */
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
