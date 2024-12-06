<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\OrgUnit\Model\RootOrgUnit;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<RootOrgUnit> */
final class GetRootOrgUnit extends MessageHandler
{
    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri('d2l://lp/organization/info')
            ->getRequest();
    }


    /** @return RootOrgUnit */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? RootOrgUnit::create([
                ...$this->jsonToArray($response),
                'PrimaryUrl' => (new GetPrimaryUrl())->invoke($this->getClient())
            ])
            : throw new \RuntimeException();
    }
}
