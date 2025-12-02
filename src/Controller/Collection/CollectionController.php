<?php

namespace WishgranterProject\Backend\Controller\Collection;

use Psr\Http\Message\ServerRequestInterface;
use WishgranterProject\Backend\Authentication\AuthenticationInterface;
use WishgranterProject\Backend\Controller\AuthenticatedController;
use WishgranterProject\Backend\Controller\ControllerBase;
use WishgranterProject\Backend\Service\ServicesManager;
use WishgranterProject\Backend\Service\CollectionManager;
use WishgranterProject\Backend\Service\Describer;
use WishgranterProject\DescriptiveManager\PlaylistManager;

/**
 * Base collection controller.
 */
abstract class CollectionController extends AuthenticatedController
{
    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Authentication\AuthenticationInterface $authentication
     *   Authentication service.
     * @param WishgranterProject\Backend\Service\CollectionManager $collectionManager
     *   Collection manager service.
     * @param WishgranterProject\Backend\Service\Describer $describer
     *   The describer service.
     */
    public function __construct(
        protected AuthenticationInterface $authentication,
        protected CollectionManager $collectionManager,
        protected Describer $describer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new (get_called_class())(
            $servicesManager->get('authentication'),
            $servicesManager->get('collectionManager'),
            $servicesManager->get('describer')
        );
    }

    public function getCollection(ServerRequestInterface $request)
    {
        $user = $this->needsAnUser($request);
        return $this->collectionManager->getCollection($user);
    }
}
