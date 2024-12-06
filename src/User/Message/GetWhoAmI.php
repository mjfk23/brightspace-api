<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\User\Model\WhoAmI;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<WhoAmI> */
final class GetWhoAmI extends MessageHandler
{
    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri('d2l://lp/users/whoami')
            ->getRequest();
    }


    /** @return WhoAmI */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? WhoAmI::create($this->jsonToArray($response))
            : throw new \RuntimeException();
    }
}
