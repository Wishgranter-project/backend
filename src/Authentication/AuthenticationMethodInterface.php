<?php

namespace WishgranterProject\Backend\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationMethodInterface
{
    public function getUser(ServerRequestInterface $request): ?User;
}
