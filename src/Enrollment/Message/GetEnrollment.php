<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Message;

use Brightspace\Api\Core\Message\MessageHandler;
use Brightspace\Api\Enrollment\Model\UserEnrollment;
use Brightspace\Api\OrgUnit\Message\GetOrgUnit;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Brightspace\Api\User\Message\GetUser;
use Brightspace\Api\User\Model\User;
use Gadget\Http\Message\RequestBuilder;
use Gadget\Io\Cast;
use Gadget\Io\JSON;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/** @extends MessageHandler<UserEnrollment|null> */
final class GetEnrollment extends MessageHandler
{
    private int $orgUnitId = 0;
    private int $userId = 0;
    private OrgUnit|null $orgUnit = null;
    private User|null $user = null;


    public function __construct(
        int|OrgUnit $orgUnit,
        int|User $user
    ) {
        list ($this->orgUnitId, $this->orgUnit) = is_int($orgUnit)
            ? [$orgUnit, null]
            : [$orgUnit->Identifier, $orgUnit];

        list ($this->userId, $this->user) = is_int($user)
            ? [$user, null]
            : [$user->UserId, $user];
    }


    protected function createRequest(RequestBuilder $requestBuilder): ServerRequestInterface
    {
        return $requestBuilder
            ->setMethod('GET')
            ->setUri("d2l://lp/enrollments/orgUnits/{$this->orgUnitId}/users/{$this->userId}")
            ->getRequest();
    }


    /** @return UserEnrollment|null */
    protected function handleResponse(
        ResponseInterface $response,
        ServerRequestInterface $request
    ): mixed {
        return match ($response->getStatusCode()) {
            200 => UserEnrollment::create([
                ...Cast::toArray(JSON::decode($response->getBody()->getContents())),
                'OrgUnit' => $this->user ?? (new GetOrgUnit($this->orgUnitId))->invoke($this->getClient()),
                'User' => $this->orgUnit ?? (new GetUser($this->userId))->invoke($this->getClient())
            ]),
            404 => null,
            default => throw new \RuntimeException()
        };
    }
}
