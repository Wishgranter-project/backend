<?php

namespace WishgranterProject\Backend\Server;

use AdinanCenci\Router\Instantiator\InstantiatorInterface;
use WishgranterProject\Backend\Service\ServicesManager;

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
     * @var WishgranterProject\Backend\Service\ServicesManager
     */
    protected ServicesManager $serviceManager;

    /**
     * Constructor.
     *
     * @param WishgranterProject\Backend\Service\ServicesManager $serviceManager.
     *   Service manager.
     */
    public function __construct(ServicesManager $serviceManager)
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
