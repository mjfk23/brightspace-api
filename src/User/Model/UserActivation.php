<?php

declare(strict_types=1);

namespace Brightspace\Api\User\Model;

use Gadget\Io\Cast;

final class UserActivation
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            IsActive: Cast::toBool($values['IsActive'] ?? null)
        );
    }


    public function __construct(public bool $IsActive)
    {
    }
}
