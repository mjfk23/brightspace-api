<?php

declare(strict_types=1);

namespace Brightspace\Api\Core;

use Brightspace\Api\Auth\Middleware\AuthMiddleware;
use Brightspace\Api\Core\Middleware\UriMiddleware;
use Gadget\Http\ApiClient;
use Gadget\Http\OAuth\OAuthMiddleware;
use Gadget\Util\Stack;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

final class BrightspaceApiClient extends ApiClient
{
    /**
     * @param ClientInterface $client
     * @param ServerRequestFactoryInterface $factory
     * @param UriMiddleware $uriMiddleware
     * @param AuthMiddleware $authMiddleware
     * @param OAuthMiddleware $oauthMiddleware
     * @param Stack<MiddlewareInterface>|null $middlewareStack
     */
    public function __construct(
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
}
