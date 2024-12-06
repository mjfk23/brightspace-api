<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Model;

use Gadget\Io\Cast;

final class WhoAmI
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            Identifier: Cast::toString($values['Identifier'] ?? null),
            FirstName: Cast::toString($values['FirstName'] ?? null),
            LastName: Cast::toString($values['LastName'] ?? null),
            UniqueName: Cast::toString($values['UniqueName'] ?? null),
            ProfileIdentifier: Cast::toString($values['ProfileIdentifier'] ?? null),
            Pronouns: Cast::toString($values['Pronouns'] ?? null),
        );
    }


    public function __construct(
        public string $Identifier,
        public string $FirstName,
        public string $LastName,
        public string $UniqueName,
        public string $ProfileIdentifier,
        public string $Pronouns,
    ) {
    }
}
