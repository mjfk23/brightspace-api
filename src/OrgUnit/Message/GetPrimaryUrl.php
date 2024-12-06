<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<string> */
final class GetPrimaryUrl extends MessageHandler
{
    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri('d2l://lp/organization/primary-url')
            ->getRequest();
    }


    /** @return string */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? $response->getBody()->getContents()
            : throw new \RuntimeException();
    }
}
