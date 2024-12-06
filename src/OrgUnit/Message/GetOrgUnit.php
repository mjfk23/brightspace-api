<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<OrgUnit|null> */
final class GetOrgUnit extends MessageHandler
{
    public function __construct(private int $orgUnitId)
    {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/orgstructure/{$this->orgUnitId}")
            ->getRequest();
    }


    /** @return OrgUnit|null */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return match ($response->getStatusCode()) {
            200 => OrgUnit::create($this->jsonToArray($response)),
            404 => null,
            default => throw new \RuntimeException()
        };
    }
}
