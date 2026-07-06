<?php

namespace WishgranterProject\Backend\Server;

use AdinanCenci\Router\Caller\Handler\ClassHandler as Base;
use AdinanCenci\Router\Caller\Exception\CallbackException;

class CustomClassHandler extends Base
{
    /**
     * {@inherit}
     */
    public function handle(mixed $callback, array $parameters = []): mixed
    {
        $controller = $this->attemptToInstantiate($callback);

        $refObject = new \ReflectionObject($controller);
        if (! $refObject->hasMethod('__invoke')) {
            throw new CallbackException(get_class($callback) . ' does not implement ::__invoke()');
        }

        $access = $controller->getAccess($parameters['request']);

        $response = $access->isAllowed()
            ? call_user_func_array($controller, $parameters)
            : $controller->deniedResponse($access);

        return $response;
    }
}
