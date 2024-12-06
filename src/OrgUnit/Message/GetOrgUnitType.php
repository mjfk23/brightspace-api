<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\OrgUnit\Model\OrgUnitType;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<OrgUnitType|null> */
final class GetOrgUnitType extends MessageHandler
{
    public function __construct(private int $orgUnitTypeId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/outypes/{$this->orgUnitTypeId}")
            ->getRequest();
    }


    /** @return OrgUnitType|null */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return match ($response->getStatusCode()) {
            200 => OrgUnitType::create($this->jsonToArray($response)),
            404 => null,
            default => throw new \RuntimeException()
        };
    }
}
