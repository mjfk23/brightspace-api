<?php

declare(strict_types=1);

namespace Brightspace\Api\Core\Model;

use Gadget\Io\Cast;

/**
 * @template T
 */
final class PagedResultSet
{
    /**
     * @param mixed $values
     * @param (callable(mixed $v): T) $toValue
     * @return self<T>
     */
    public static function create(
        mixed $values,
        callable $toValue
    ): self {
        $values = Cast::toArray($values);
        return new self(
            pagingInfo: PagingInfo::create($values['PagingInfo'] ?? null),
            items: Cast::toTypedArray($values['Items'] ?? [], $toValue)
        );
    }


    /**
     * @param PagingInfo $pagingInfo
     * @param T[] $items
     */
    public function __construct(
        public PagingInfo $pagingInfo,
        public array $items
    ) {
    }
}
