<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome\Model;

use Gadget\Io\Cast;

final class OutcomeDetail implements \JsonSerializable
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            id: Cast::toString($values['id'] ?? null),
            description: Cast::toString($values['description'] ?? null),
            children: Cast::toTypedArray(
                Cast::toArray($values['children'] ?? []),
                self::create(...)
            ),
            parentId: Cast::toValueOrNull($values['id'] ?? null, Cast::toString(...))
        );
    }


    /** @param OutcomeDetail[] $children */
    public function __construct(
        public string $id,
        public string $description,
        public array $children = [],
        public string|null $parentId = null
    ) {
        foreach ($this->children as $child) {
            $child->parentId ??= $this->id;
        }
    }


    /** @return array{
     *    id:string,
     *    children:OutcomeDetail[]
     *  }
     */
    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'children' => array_values($this->children)
        ];
    }
}
