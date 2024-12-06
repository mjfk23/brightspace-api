<?php

declare(strict_types=1);

namespace Brightspace\Api;

use Brightspace\Api\Enrollment\EnrollmentApi;
use Brightspace\Api\OrgUnit\OrgUnitApi;
use Brightspace\Api\Outcome\OutcomeApi;
use Brightspace\Api\User\UserApi;

final class BrightspaceApi
{
    /**
     * @param EnrollmentApi $enrollment
     * @param OrgUnitApi $orgUnit
     * @param OutcomeApi $outcome
     * @param UserApi $user
     */
    public function __construct(
        public readonly EnrollmentApi $enrollment,
        public readonly OrgUnitApi $orgUnit,
        public readonly OutcomeApi $outcome,
        public readonly UserApi $user
    ) {
    }
}
