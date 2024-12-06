<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,OrgUnit>> */
final class ListOrgUnitAncestors extends MessageHandler
{
    public function __construct(
        private int $orgUnitId,
        private int|null $orgUnitType = null,
        private bool $onlyParents = false
    ) {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri(
                $this->onlyParents
                    ? "d2l://lp/orgstructure/{$this->orgUnitId}/parents/"
                    : "d2l://lp/orgstructure/{$this->orgUnitId}/ancestors/"
            )
            ->setQueryParams(['ouTypeId' => $this->orgUnitType])
            ->getRequest();
    }


    /** @return iterable<int,OrgUnit> */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $result = Cast::toTypedArray(
            $this->jsonToArray($response),
            OrgUnit::create(...)
        );

        foreach ($result as $item) {
            yield $item->Identifier => $item;
        }
    }
}
