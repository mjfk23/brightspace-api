<?php

declare(strict_types=1);

namespace Brightspace\Api\Enrollment\Model;

use Gadget\Io\Cast;

final class UserEnrollmentRole
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            Id: Cast::toInt($values['Id'] ?? null),
            Code: Cast::toValueOrNull($values['Code'] ?? null, Cast::toString(...)),
            Name: Cast::toValueOrNull($values['Name'] ?? null, Cast::toString(...))
        );
    }


    public function __construct(
        public int $Id = 0,
        public string|null $Code = null,
        public string|null $Name = null
    ) {
    }
}
