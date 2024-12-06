<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome;

use Brightspace\Api\Core\Client\ApiClient;
use Brightspace\Api\Outcome\Message\GetRegistry;
use Brightspace\Api\Outcome\Message\GetRegistryId;
use Brightspace\Api\Outcome\Message\UpdateRegistry;
use Brightspace\Api\Outcome\Model\OutcomeRegistry;

final class OutcomeApi extends ApiClient
{
    /**
     * @param int $orgUnitId
     * @return string
     */
    public function getId(int $orgUnitId): string
    {
        return $this->invoke(new GetRegistryId($orgUnitId));
    }


    /**
     * @param int|string $orgUnitOrRegistryId
     * @return OutcomeRegistry|null
     */
    public function get(int|string $orgUnitOrRegistryId): OutcomeRegistry|null
    {
        return $this->invoke(new GetRegistry(
            is_string($orgUnitOrRegistryId)
                ? $orgUnitOrRegistryId
                : $this->getId($orgUnitOrRegistryId)
        ));
    }


    /**
     * @param OutcomeRegistry $registry
     * @return bool
     */
    public function update(OutcomeRegistry $registry): bool
    {
        return $this->invoke(new UpdateRegistry($registry));
    }
}
