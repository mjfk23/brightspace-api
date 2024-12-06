<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\OrgUnit\Model\OrgUnitType;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,OrgUnitType>> */
final class ListOrgUnitTypes extends MessageHandler
{
    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri('d2l://lp/outypes/')
            ->getRequest();
    }


    /** @return iterable<int,OrgUnitType> */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $results = Cast::toTypedArray(
            $this->jsonToArray($response),
            OrgUnitType::create(...)
        );

        foreach ($results as $type) {
            yield $type->Id => $type;
        }
    }
}
