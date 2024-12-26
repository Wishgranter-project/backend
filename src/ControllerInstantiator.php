<?php

namespace WishgranterProject\Backend;

use AdinanCenci\Router\Instantiator\InstantiatorInterface;
use WishgranterProject\Backend\Service\ServicesManager;

class ControllerInstantiator implements InstantiatorInterface
{
    protected ServicesManager $serviceManager;

    public function __construct(ServicesManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function instantiate(string $className): object
    {
        return call_user_func_array([$className, 'instantiate'], [$this->serviceManager]);
    }
}
