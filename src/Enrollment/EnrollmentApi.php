<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment;

use Brightspace\Api\Core\Client\ApiClient;
use Brightspace\Api\Enrollment\Message\EnrollUser;
use Brightspace\Api\Enrollment\Message\GetEnrollment;
use Brightspace\Api\Enrollment\Message\ListEnrollmentOrgUnits;
use Brightspace\Api\Enrollment\Message\ListEnrollmentUsers;
use Brightspace\Api\Enrollment\Message\PinEnrollment;
use Brightspace\Api\Enrollment\Message\UnenrollUser;
use Brightspace\Api\Enrollment\Message\UnpinEnrollment;
use Brightspace\Api\Enrollment\Model\UserEnrollment;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Brightspace\Api\User\Model\User;

/**
 * API endpoints around enrollments: assigning users to the various defined organizational units.
 *
 * @see https://docs.valence.desire2learn.com/res/enroll.html
 */
final class EnrollmentApi extends ApiClient
{
    /**
     * Retrieve the collection of org units the identified user is enrolled in.
     *
     * @param int|User $user User ID or `User` object.
     * @param int|int[]|null $orgUnitType Optional. Filter list to specific org unit types.
     * @param int|null $roleId Optional. Filter list to a specific user role.
     * @param string|null $bookmark Optional. Bookmark to where to start result set
     * @return iterable<int,UserEnrollment> Result set of {`OrgUnitId` => `UserEnrollment`}
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#get--d2l-api-lp-(version)-enrollments-users-(userId)-orgUnits-
     */
    public function listOrgUnits(
        int|User $user,
        int|array|null $orgUnitType = null,
        int|null $roleId = null,
        string|null $bookmark = null
    ): iterable {
        return $this->invoke(new ListEnrollmentOrgUnits(
            $user,
            $orgUnitType,
            $roleId,
            $bookmark
        ));
    }


    /**
     * Retrieve the collection of users enrolled in the identified org unit.
     *
     * @param int|OrgUnit $orgUnit Org unit ID or `OrgUnit` object.
     * @param int|null $roleId Optional. Filter list to a specific user role.
     * @param bool|null $isActive Optional. Filter list to only active or inactive users.
     * @param string|null $bookmark Optional. Bookmark to where to start result set
     * @return iterable<int,UserEnrollment> Result set of {`UserId` => `UserEnrollment`}
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#get--d2l-api-lp-(version)-enrollments-orgUnits-(orgUnitId)-users-
     */
    public function listUsers(
        int|OrgUnit $orgUnit,
        int|null $roleId = null,
        bool|null $isActive = null,
        string|null $bookmark = null
    ): iterable {
        return $this->invoke(new ListEnrollmentUsers(
            $orgUnit,
            $roleId,
            $isActive,
            $bookmark
        ));
    }


    /**
     * Retrieve enrollment details for a user in the provided org unit.
     *
     * @param int|OrgUnit $orgUnit Org unit ID or `OrgUnit` object.
     * @param int|User $user User ID or `User` object.
     * @return UserEnrollment|null `UserEnrollment`, or `null` if no such org unit or no such user enrolled in org unit.
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#get--d2l-api-lp-(version)-enrollments-orgUnits-(orgUnitId)-users-(userId)
     */
    public function get(
        int|OrgUnit $orgUnit,
        int|User $user
    ): UserEnrollment|null {
        return $this->invoke(new GetEnrollment(
            $orgUnit,
            $user
        ));
    }


    /**
     * Create or update a new enrollment for a user.
     *
     * @param int $orgUnitId Org unit ID.
     * @param int $userId User ID.
     * @param int $roleId Role ID.
     * @return bool Indicator whether the action succeeded
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#post--d2l-api-lp-(version)-enrollments-
     */
    public function enroll(
        int $orgUnitId,
        int $userId,
        int $roleId
    ): bool {
        return $this->invoke(new EnrollUser($orgUnitId, $userId, $roleId));
    }


    /**
     * Delete a user's enrollment in a provided org unit.
     *
     * @param int $orgUnitId Org unit ID.
     * @param int $userId User ID.
     * @return bool Indicator whether the action succeeded
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#delete--d2l-api-lp-(version)-enrollments-orgUnits-(orgUnitId)-users-(userId)
     */
    public function unenroll(
        int $orgUnitId,
        int $userId
    ): bool {
        return $this->invoke(new UnenrollUser($orgUnitId, $userId));
    }


    /**
     * Pin an org unit to the top of the list for a user's enrollments.
     *
     * @param int $orgUnitId Org unit ID.
     * @param int $userId User ID.
     * @return bool Indicator whether the action succeeded
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#post--d2l-api-lp-(version)-enrollments-orgUnits-(orgUnitId)-users-(userId)-pin
     */
    public function pin(
        int $orgUnitId,
        int $userId
    ): bool {
        return $this->invoke(new PinEnrollment($orgUnitId, $userId));
    }


    /**
     * Remove the pin from the provided org unit for specific user.
     *
     * @param int $orgUnitId Org unit ID.
     * @param int $userId User ID.
     * @return bool Indicator whether the action succeeded
     *
     * @see https://docs.valence.desire2learn.com/res/enroll.html#delete--d2l-api-lp-(version)-enrollments-orgUnits-(orgUnitId)-users-(userId)-pin
     */
    public function unpin(
        int $orgUnitId,
        int $userId
    ): bool {
        return $this->invoke(new UnpinEnrollment($orgUnitId, $userId));
    }
}
