<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Model;

use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Brightspace\Api\User\Model\User;
use Gadget\Io\Cast;

final class UserEnrollment
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            OrgUnit: Cast::toValueOrNull($values['OrgUnit'] ?? null, OrgUnit::create(...)),
            User: Cast::toValueOrNull($values['User'] ?? null, User::create(...)),
            Role: Cast::toValueOrNull($values['Role'] ?? null, UserEnrollmentRole::create(...))
        );
    }


    public function __construct(
        public OrgUnit|null $OrgUnit = null,
        public User|null $User = null,
        public UserEnrollmentRole|null $Role = null
    ) {
    }
}
