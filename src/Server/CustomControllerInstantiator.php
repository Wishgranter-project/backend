<?php

namespace WishgranterProject\Backend\Server;

use AdinanCenci\Router\Instantiator\InstantiatorInterface;
use WishgranterProject\Backend\Service\ServiceLocator;

/**
 * Custom class to instantiate the controllers.
 *
 * For the router object, allow us to implement dependency injection.
 */
class CustomControllerInstantiator implements InstantiatorInterface
{
    /**
     * Service manager.
     *
     * With the services to inject in the controllers.
     *
     * @var WishgranterProject\Backend\Service\ServiceLocator
     */
    protected ServiceLocator $serviceManager;

    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Service\ServiceLocator $serviceManager.
     *   Service manager.
     */
    public function __construct(ServiceLocator $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * {@inheritdoc}
     */
    public function instantiate(string $className): object
    {
        return call_user_func_array([$className, 'instantiate'], [$this->serviceManager]);
    }
}
