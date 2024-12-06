<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<bool> */
final class CreateOrgUnitRelationship extends MessageHandler
{
    public function __construct(
        private int $childOrgUnit,
        private int $parentOrgUnit
    ) {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('POST')
            ->setUri(sprintf(
                'd2l://lp/orgstructure/%s/parents/',
                $this->childOrgUnit,
            ))
            ->setBody(RequestBuilder::JSON, $this->parentOrgUnit)
            ->getRequest();
    }


    /** @return bool */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return $response->getStatusCode() === 200;
    }
}
