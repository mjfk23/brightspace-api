<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Model;

use Gadget\Io\Cast;

final class RootOrgUnit
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            Identifier: Cast::toString($values['Identifier'] ?? null),
            Name: Cast::toString($values['Name'] ?? null),
            TimeZone: Cast::toString($values['TimeZone'] ?? null),
            PrimaryUrl: Cast::toString($values['PrimaryUrl'] ?? null)
        );
    }


    public function __construct(
        public string $Identifier,
        public string $Name,
        public string $TimeZone,
        public string|null $PrimaryUrl = null
    ) {
    }
}
