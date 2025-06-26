<?php

namespace WishgranterProject\Backend\Controller\HomePage;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Helper\JsonResource;
use WishgranterProject\Backend\Controller\ControllerBase;

class HomePage extends ControllerBase
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->jsonResource('home page')
            ->renderResponse();
    }
}
