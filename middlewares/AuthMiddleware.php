<?php

namespace App\middlewares;

use App\Controllers\UsersController;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if ((new UsersController)->checkLoginCookie() !== false) return $handler->handle($request);
        return new RedirectResponse('/login');
    }
}
