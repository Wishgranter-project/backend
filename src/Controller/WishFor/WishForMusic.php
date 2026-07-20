<?php

namespace WishgranterProject\Backend\Controller\WishFor;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use WishgranterProject\MusicRadar\Radar;
use WishgranterProject\MusicProbe\Description;
use WishgranterProject\Backend\Authentication\AuthenticationManagerInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServiceLocator;

/**
 * Given the description of a music, searches for playable media.
 */
class WishForMusic extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationManagerInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\MusicRadar\Radar $radar
     *   The radar service.
     */
    public function __construct(
        protected AuthenticationManagerInterface $authentication,
        protected Radar $radar,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServiceLocator $serviceLocator): ControllerBase
    {
        return new self(
            $serviceLocator->get('authentication'),
            $serviceLocator->get('radar'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $description = $this->buildDescription($request);
        $search      = $this->radar->searchFor($description);
        $search->addDefaultCriteria();

        $searchResults = $search->find();

        $data = [];
        foreach ($searchResults as $item) {
            $data[] = $item->getResource()->toArray();
        }

        $debug = [
            'description' => $description->toArray()
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
