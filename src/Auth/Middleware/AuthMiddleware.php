<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Middleware;

use Brightspace\Api\Auth\Cache\LoginTokenCache;
use Brightspace\Api\Auth\Model\AuthConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @param AuthConfig $config
     * @param LoginTokenCache $cache
     */
    public function __construct(
        private AuthConfig $config,
        private LoginTokenCache $cache
    ) {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $handler->handle($this->processOauth($this->processLogin($request)));
    }


    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    private function processLogin(ServerRequestInterface $request): ServerRequestInterface
    {
        $key = ($request->getUri()->getHost() === $this->config->hostName)
            ? $request->getAttribute($this->config->loginKeyAttrName)
            : false;
        $key = match (true) {
            is_string($key) => $key,
            $key === true => $this->config->defaultLoginKey,
            default => false
        };
        $request = $request->withAttribute($this->config->loginKeyAttrName, $key);
        $loginToken = is_string($key) ? $this->cache->get($key) : null;

        return is_string($loginToken)
            ? $request
                ->withHeader('Cookie', $loginToken)
                ->withAttribute($this->config->keyAttrName, false)
            : $request;
    }


    /**
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    private function processOauth(ServerRequestInterface $request): ServerRequestInterface
    {
        $key = $request->getAttribute($this->config->keyAttrName)
            ?? ($request->getUri()->getHost() === $this->config->hostName);
        $key = match (true) {
            is_string($key) => $key,
            $key === true => $this->config->defaultKey,
            default => false
        };
        $request = $request->withAttribute($this->config->keyAttrName, $key);

        return is_string($key)
            ? $request->withAttribute($this->config->loginKeyAttrName, false)
            : $request;
    }
}
