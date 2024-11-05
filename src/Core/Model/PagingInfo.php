<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Model;

use Gadget\Io\Cast;

final class PagingInfo
{
    /**
     * @param mixed $values
     * @return self
     */
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            bookmark: Cast::toValueOrNull($values['Bookmark'] ?? null, Cast::toString(...)),
            hasMoreItems: Cast::toBool($values['HasMoreItems'] ?? null)
        );
    }


    /**
     * @param string|null $bookmark
     * @param bool $hasMoreItems
     */
    public function __construct(
        public string|null $bookmark = null,
        public bool $hasMoreItems = false
    ) {
    }
}
