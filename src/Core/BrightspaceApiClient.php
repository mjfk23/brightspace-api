<?php

declare(strict_types=1);

namespace Brightspace\Api\Core;

use Brightspace\Api\Auth\Middleware\AuthMiddleware;
use Brightspace\Api\Auth\Model\AuthConfig;
use Brightspace\Api\Core\Middleware\UriMiddleware;
use Gadget\Http\ApiClient;
use Gadget\Http\OAuth\OAuthMiddleware;
use Gadget\Util\Stack;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

final class BrightspaceApiClient extends ApiClient
{
    /**
     * @param AuthConfig $authConfig
     * @param ClientInterface $client
     * @param ServerRequestFactoryInterface $factory
     * @param UriMiddleware $uriMiddleware
     * @param AuthMiddleware $authMiddleware
     * @param OAuthMiddleware $oauthMiddleware
     * @param Stack<MiddlewareInterface>|null $middlewareStack
     */
    public function __construct(
        private AuthConfig $authConfig,
        ClientInterface $client,
        ServerRequestFactoryInterface $factory,
        UriMiddleware $uriMiddleware,
        AuthMiddleware $authMiddleware,
        OAuthMiddleware $oauthMiddleware,
        Stack|null $middlewareStack = null
    ) {
        parent::__construct($client, $factory, $middlewareStack);
        $this->getMiddlewareStack()->push($uriMiddleware);
        $this->getMiddlewareStack()->push($authMiddleware);
        $this->getMiddlewareStack()->push($oauthMiddleware);
    }


    /**
     * @param ServerRequestInterface $request
     * @param mixed $default
     * @return string|null
     */
    public function getLoginKey(
        ServerRequestInterface $request,
        mixed $default = null
    ): string|null {
        $loginKey = $request->getAttribute($this->authConfig->loginKeyAttrName, $default);
        return match (true) {
            is_string($loginKey) => $loginKey,
            $loginKey === true => $this->authConfig->defaultLoginKey,
            default => null
        };
    }


    /**
     * @param ServerRequestInterface $request
     * @param string|bool|null $loginKey
     * @return ServerRequestInterface
     */
    public function setLoginKey(
        ServerRequestInterface $request,
        string|bool|null $loginKey
    ): ServerRequestInterface {
        return $request->withAttribute(
            $this->authConfig->loginKeyAttrName,
            $loginKey
        );
    }


    /**
     * @param ServerRequestInterface $request
     * @param mixed $default
     * @return string|null
     */
    public function getOauthKey(
        ServerRequestInterface $request,
        mixed $default = null
    ): string|null {
        $oauthKey = $request->getAttribute($this->authConfig->keyAttrName, $default);
        return match (true) {
            is_string($oauthKey) => $oauthKey,
            $oauthKey === true => $this->authConfig->defaultKey,
            default => null
        };
    }


    /**
     * @param ServerRequestInterface $request
     * @param string|bool|null $oauthKey
     * @return ServerRequestInterface
     */
    public function setOauthKey(
        ServerRequestInterface $request,
        string|bool|null $oauthKey
    ): ServerRequestInterface {
        return $request->withAttribute(
            $this->authConfig->keyAttrName,
            $oauthKey
        );
    }
}
