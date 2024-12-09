<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<bool> */
final class DeleteOrgUnitRelationship extends MessageHandler
{
    public function __construct(
        private int $childOrgUnit,
        private int $parentOrgUnit
    ) {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('DELETE')
            ->setUri("d2l://lp/orgstructure/{$this->childOrgUnit}/parents/{$this->parentOrgUnit}")
            ->getRequest();
    }


    /** @return bool */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200
            ? true
            : throw new \RuntimeException();
    }
}
