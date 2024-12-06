<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Core\Model\PagedResultSet;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Gadget\Http\Client\Client;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\JSON;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,OrgUnit>> */
final class ListOrgUnitDescendants extends MessageHandler
{
    public function __construct(
        private int $orgUnitId,
        private int|null $orgUnitType = null,
        private string|null $bookmark = null,
        private bool $onlyChildren = false
    ) {
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri(
                $this->onlyChildren
                    ? "d2l://lp/orgstructure/{$this->orgUnitId}/children/paged/"
                    : "d2l://lp/orgstructure/{$this->orgUnitId}/descendants/paged/"
            )
            ->setQueryParams([
                'ouTypeId' => $this->orgUnitType,
                'bookmark' => $this->bookmark
            ])
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

        $result = PagedResultSet::create(
            $this->jsonToArray($response),
            OrgUnit::create(...)
        );

        foreach ($result->items as $item) {
            yield $item->Identifier => $item;
        }

        $this->bookmark = $result->pagingInfo->bookmark;
        if ($this->bookmark !== null && $this->bookmark !== '') {
            yield from $this->invoke($this->getClient());
        }
    }
}
