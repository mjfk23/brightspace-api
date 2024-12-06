<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Model;

use Gadget\Io\Cast;

final class OrgUnit
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            Identifier: Cast::toInt($values['Identifier'] ?? $values['Id'] ?? null),
            Name: Cast::toString($values['Name'] ?? null),
            Code: Cast::toValueOrNull($values['Code'] ?? null, Cast::toString(...)),
            Path: Cast::toValueOrNull($values['Path'] ?? null, Cast::toString(...)),
            Type: OrgUnitType::create($values['Type'] ?? null)
        );
    }


    public function __construct(
        public int $Identifier = 0,
        public string $Name = '',
        public string|null $Code = null,
        public string|null $Path = null,
        public OrgUnitType $Type = new OrgUnitType()
    ) {
    }
}
