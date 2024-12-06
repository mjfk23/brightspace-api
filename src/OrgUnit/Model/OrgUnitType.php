<?php

declare(strict_types=1);

namespace Brightspace\Api\OrgUnit\Model;

use Gadget\Io\Cast;

final class OrgUnitType
{
    /** @var int */
    public const ORGANIZATION = 1;
    /** @var int */
    public const COURSE_TEMPLATE = 2;
    /** @var int */
    public const COURSE_OFFERING = 3;
    /** @var int */
    public const GROUP = 4;
    /** @var int */
    public const SECTION = 4;
    /** @var int */
    public const SEMESTER = 5;


    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            Id: Cast::toInt($values['Id'] ?? null),
            Code: Cast::toString($values['Code'] ?? null),
            Name: Cast::toString($values['Name'] ?? null)
        );
    }


    public function __construct(
        public int $Id = 0,
        public string $Code = '',
        public string $Name = ''
    ) {
    }
}
