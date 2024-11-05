<?php

declare(strict_types=1);

namespace Brightspace\Api\Auth\Middleware;

use Brightspace\Api\Auth\LoginTokenCache;
use Brightspace\Api\Auth\Model\OAuthConfig;
use Gadget\Http\OAuth\OAuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoginMiddleware implements MiddlewareInterface
{
    /**
     * @param OAuthConfig $config
     * @param LoginTokenCache $cache
     * @param OAuthMiddleware $oauthMiddleware
     */
    public function __construct(
        private OAuthConfig $config,
        private LoginTokenCache $cache,
        private OAuthMiddleware $oauthMiddleware
    ) {
    }


    /** @inheritdoc */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $token = $this->getToken($this->getKey($request));
        return ($token !== null)
            ? $handler->handle($request->withHeader('Cookie', $token))
            : $this->oauthMiddleware->process($request, $handler);
    }


    /**
     * @param ServerRequestInterface $request
     * @return string|null
     */
    private function getKey(ServerRequestInterface $request): string|null
    {
        $key = ($request->getUri()->getHost() === $this->config->hostName)
            ? $request->getAttribute($this->config->loginKeyAttrName)
            : null;

        return match (true) {
            is_string($key) => $key,
            $key === true => $this->config->defaultKey,
            default => null
        };
    }


    /**
     * @param string|null $key
     * @return string|null
     */
    private function getToken(string|null $key): string|null
    {
        return is_string($key) ? $this->cache->get($key) : null;
    }
}
