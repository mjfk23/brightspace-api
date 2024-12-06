<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Core\Model\PagedResultSet;
use Brightspace\Api\Enrollment\Model\UserEnrollment;
use Brightspace\Api\OrgUnit\Message\GetOrgUnit;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,UserEnrollment>> */
final class ListEnrollmentUsers extends MessageHandler
{
    private int $orgUnitId = 0;
    private OrgUnit|null $orgUnit = null;


    public function __construct(
        int|OrgUnit $orgUnit,
        private int|null $roleId = null,
        private bool|null $isActive = null,
        private string|null $bookmark = null
    ) {
        list ($this->orgUnitId, $this->orgUnit) = is_int($orgUnit)
            ? [$orgUnit, null]
            : [$orgUnit->Identifier, $orgUnit];
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/enrollments/orgUnits/{$this->orgUnitId}/users/")
            ->setQueryParams([
                'roleId' => $this->roleId,
                'isActive' => $this->isActive,
                'bookmark' => $this->bookmark,
            ])
            ->getRequest()
            ;
    }


    /** @return iterable<int,UserEnrollment> */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException();
        }

        $this->orgUnit ??= (new GetOrgUnit($this->orgUnitId))->invoke($this->getClient());

        $result = PagedResultSet::create(
            $this->jsonToArray($response),
            fn(mixed $values): UserEnrollment => UserEnrollment::create([
                ...Cast::toArray($values),
                'OrgUnit' => $this->orgUnit
            ])
        );

        foreach ($result->items as $enrollment) {
            if ($enrollment->User !== null) {
                yield $enrollment->User->UserId => $enrollment;
            }
        }

        $this->bookmark = $result->pagingInfo->bookmark;
        if ($this->bookmark !== null && $this->bookmark !== '') {
            yield from $this->invoke($this->getClient());
        }
    }
}
