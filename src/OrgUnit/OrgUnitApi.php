<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit;

use Brightspace\Api\Core\Client\ApiClient;
use Brightspace\Api\OrgUnit\Message\CreateOrgUnitRelationship;
use Brightspace\Api\OrgUnit\Message\DeleteOrgUnitRelationship;
use Brightspace\Api\OrgUnit\Message\GetOrgUnit;
use Brightspace\Api\OrgUnit\Message\GetOrgUnitType;
use Brightspace\Api\OrgUnit\Message\GetRootOrgUnit;
use Brightspace\Api\OrgUnit\Message\ListOrgUnitAncestors;
use Brightspace\Api\OrgUnit\Message\ListOrgUnitDescendants;
use Brightspace\Api\OrgUnit\Message\ListOrgUnits;
use Brightspace\Api\OrgUnit\Message\ListOrgUnitTypes;
use Brightspace\Api\OrgUnit\Model\OrgUnit;
use Brightspace\Api\OrgUnit\Model\OrgUnitType;
use Brightspace\Api\OrgUnit\Model\RootOrgUnit;

final class OrgUnitApi extends ApiClient
{
    /** @return RootOrgUnit */
    public function getRoot(): RootOrgUnit
    {
        return $this->invoke(new GetRootOrgUnit());
    }


    /**
     * @param int|null $orgUnitType
     * @param string|null $orgUnitCode
     * @param string|null $orgUnitName
     * @param string|null $bookmark
     * @param string|null $exactOrgUnitCode
     * @param string|null $exactOrgUnitName
     * @return iterable<int,OrgUnit>
     */
    public function list(
        int|null $orgUnitType = null,
        string|null $orgUnitCode = null,
        string|null $orgUnitName = null,
        string|null $bookmark = null,
        string|null $exactOrgUnitCode = null,
        string|null $exactOrgUnitName = null
    ): iterable {
        return $this->invoke(new ListOrgUnits(
            $orgUnitType,
            $orgUnitCode,
            $orgUnitName,
            $bookmark,
            $exactOrgUnitCode,
            $exactOrgUnitName
        ));
    }


    /**
     * @param int $orgUnitId
     * @return OrgUnit|null
     */
    public function get(int $orgUnitId): OrgUnit|null
    {
        return $this->invoke(new GetOrgUnit($orgUnitId));
    }


    /**
     * @param int $orgUnitId
     * @param int|null $orgUnitType
     * @return iterable<int,OrgUnit>
     */
    public function listAncestors(
        int $orgUnitId,
        int|null $orgUnitType = null
    ): iterable {
        return $this->invoke(new ListOrgUnitAncestors(
            $orgUnitId,
            $orgUnitType,
            false
        ));
    }


    /**
     * @param int $orgUnitId
     * @param int|null $orgUnitType
     * @param string|null $bookmark
     * @return iterable<int,OrgUnit>
     */
    public function listDescendants(
        int $orgUnitId,
        int|null $orgUnitType = null,
        string|null $bookmark = null
    ): iterable {
        return $this->invoke(new ListOrgUnitDescendants(
            $orgUnitId,
            $orgUnitType,
            $bookmark,
            false
        ));
    }


    /**
     * @param int $orgUnitId
     * @param int|null $orgUnitType
     * @return iterable<int,OrgUnit>
     */
    public function listParents(
        int $orgUnitId,
        int|null $orgUnitType = null
    ): iterable {
        return $this->invoke(new ListOrgUnitAncestors(
            $orgUnitId,
            $orgUnitType,
            true
        ));
    }


    /**
     * @param int $orgUnitId
     * @param int|null $orgUnitType
     * @param string|null $bookmark
     * @return iterable<int,OrgUnit>
     */
    public function listChildren(
        int $orgUnitId,
        int|null $orgUnitType = null,
        string|null $bookmark = null
    ): iterable {
        return $this->invoke(new ListOrgUnitDescendants(
            $orgUnitId,
            $orgUnitType,
            $bookmark,
            true
        ));
    }


    /**
     * @param int $childOrgUnitId
     * @param int $parentOrgUnitId
     * @return bool
     */
    public function createRelationship(
        int $childOrgUnitId,
        int $parentOrgUnitId
    ): bool {
        return $this->invoke(new CreateOrgUnitRelationship(
            $childOrgUnitId,
            $parentOrgUnitId
        ));
    }


    /**
     * @param int $childOrgUnitId
     * @param int $parentOrgUnitId
     * @return bool
     */
    public function deleteRelationship(
        int $childOrgUnitId,
        int $parentOrgUnitId
    ): bool {
        return $this->invoke(new DeleteOrgUnitRelationship(
            $childOrgUnitId,
            $parentOrgUnitId
        ));
    }


    /**
     * @return iterable<int,OrgUnitType>
     */
    public function listTypes(): iterable
    {
        return $this->invoke(new ListOrgUnitTypes());
    }


    /**
     * @param int $orgUnitTypeId
     * @return OrgUnitType|null
     */
    public function getType(int $orgUnitTypeId): OrgUnitType|null
    {
        return $this->invoke(new GetOrgUnitType($orgUnitTypeId));
    }
}
