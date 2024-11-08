<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Middleware;

use Brightspace\Api\Auth\Cache\LoginTokenCache;
use Brightspace\Api\Auth\Model\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LoginMiddleware implements MiddlewareInterface
{
    /**
     * @param Config $config
     * @param LoginTokenCache $cache
     */
    public function __construct(
        private Config $config,
        private LoginTokenCache $cache
    ) {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $requestKey = $request->getAttribute($this->config->loginCacheKey);

        $loginCacheKey = match (true) {
            is_string($requestKey) => $requestKey,
            $requestKey === true => $this->config->loginCacheKey,
            default => null
        };

        $loginToken = $loginCacheKey !== null
            ? $this->cache->get($loginCacheKey)
            : null;

        return $handler->handle(
            ($request->getUri()->getHost() === $this->config->hostName) && is_string($loginToken)
                ? $request
                    ->withHeader('Cookie', $loginToken)
                    ->withAttribute($this->config->tokenRequestAttr, false)
                : $request
        );
    }
}
