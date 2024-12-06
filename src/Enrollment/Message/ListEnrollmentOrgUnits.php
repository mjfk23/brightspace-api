<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Core\Model\PagedResultSet;
use Brightspace\Api\Enrollment\Model\UserEnrollment;
use Brightspace\Api\User\Message\GetUser;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<iterable<int,UserEnrollment>> */
final class ListEnrollmentOrgUnits extends MessageHandler
{
    private int $userId = 0;
    private User|null $user = null;


    /** @param int|int[]|null $orgUnitType */
    public function __construct(
        int|User $user,
        private int|array|null $orgUnitType = null,
        private int|null $roleId = null,
        private string|null $bookmark = null
    ) {
        list ($this->userId, $this->user) = is_int($user)
            ? [$user, null]
            : [$user->UserId, $user];
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/enrollments/users/{$this->userId}/orgUnits/")
            ->setQueryParams([
                'orgUnitType' => is_array($this->orgUnitType)
                    ? implode(",", $this->orgUnitType)
                    : $this->orgUnitType,
                'roleId' => $this->roleId,
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

        $this->user ??= (new GetUser($this->userId))->invoke($this->getClient());

        $result = PagedResultSet::create(
            $this->jsonToArray($response),
            fn(mixed $v): UserEnrollment => UserEnrollment::create([
                ...Cast::toArray($v),
                'User' => $this->user
            ])
        );

        foreach ($result->items as $enrollment) {
            if ($enrollment->OrgUnit !== null) {
                yield $enrollment->OrgUnit->Identifier => $enrollment;
            }
        }

        $this->bookmark = $result->pagingInfo->bookmark;
        if ($this->bookmark !== null) {
            yield from $this->invoke($this->getClient());
        }
    }
}
