<?php

declare(strict_types=1);

namespace Brightspace\Api\Core;

use Gadget\Http\ApiClient;
use Gadget\Util\Stack;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;

class BrightspaceApiClient extends ApiClient
{
    /**
     * @param ClientInterface $client
     * @param ServerRequestFactoryInterface $factory
     * @param BrightspaceApiMiddleware $brightspaceApiMiddleware
     * @param Stack<MiddlewareInterface>|null $middlewareStack
     */
    public function __construct(
        ClientInterface $client,
        ServerRequestFactoryInterface $factory,
        BrightspaceApiMiddleware $brightspaceApiMiddleware,
        Stack|null $middlewareStack = null
    ) {
        parent::__construct($client, $factory, $middlewareStack);
        $this->getMiddlewareStack()->push($brightspaceApiMiddleware);
    }
}
