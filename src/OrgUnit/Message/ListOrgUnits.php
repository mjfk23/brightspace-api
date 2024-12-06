<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Core\Model\PagedResultSet;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Gadget\Http\Message\RequestBuilder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,OrgUnit>> */
final class ListOrgUnits extends MessageHandler
{
    public function __construct(
        private int|null $orgUnitType = null,
        private string|null $orgUnitCode = null,
        private string|null $orgUnitName = null,
        private string|null $bookmark = null,
        private string|null $exactOrgUnitCode = null,
        private string|null $exactOrgUnitName = null
    ) {
        if ($this->exactOrgUnitCode !== null) {
            $this->orgUnitCode = null;
        }
        if ($this->exactOrgUnitName !== null) {
            $this->orgUnitName = null;
        }
    }


    /** @return ServerRequestInterface */
    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/orgstructure/")
            ->setQueryParams([
                'orgUnitType' => $this->orgUnitType,
                'orgUnitCode' => $this->orgUnitCode,
                'orgUnitName' => $this->orgUnitName,
                'bookmark' => $this->bookmark,
                'exactOrgUnitCode' => $this->exactOrgUnitCode,
                'exactOrgUnitName' => $this->exactOrgUnitName,
            ])
            ->getRequest()
            ;
    }


    /** @return iterable<int,OrgUnit> */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $result = PagedResultSet::create(
            $this->jsonToArray($response),
            OrgUnit::create(...)
        );

        foreach ($result->items as $orgUnit) {
            yield $orgUnit->Identifier => $orgUnit;
        }

        $this->bookmark = $result->pagingInfo->bookmark;
        if ($this->bookmark !== null) {
            yield from $this->invoke($this->getClient());
        }
    }
}
