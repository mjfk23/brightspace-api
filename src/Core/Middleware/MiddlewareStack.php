<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Middleware;

use Brightspace\Api\Auth\Middleware\LoginMiddleware;
use Gadget\Http\Middleware\MiddlewareStack as BaseMiddlewareStack;
use Gadget\Http\OAuth\Middleware\AccessTokenMiddleware;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareStack extends BaseMiddlewareStack
{
    /**
     * @param UriMiddleware $uri
     * @param LoginMiddleware $login
     * @param AccessTokenMiddleware $accessToken
     * @param MiddlewareInterface $middleware
     */
    public function __construct(
        UriMiddleware $uri,
        LoginMiddleware $login,
        AccessTokenMiddleware $accessToken,
        ...$middleware
    ) {
        parent::__construct([
            ...array_reverse(array_values($middleware)),
            $accessToken,
            $login,
            $uri
        ]);
    }
}
