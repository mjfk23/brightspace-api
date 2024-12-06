<?php

declare(strict_types=1);

namespace Brightspace\Api\Outcome\Model;

use Gadget\Io\Cast;

final class OutcomeRegistry
{
    public static function create(mixed $values): self
    {
        $values = Cast::toArray($values);
        return new self(
            id: Cast::toString($values['id'] ?? null),
            objectives: Cast::toTypedArray(
                $values['objectives'] ?? [],
                OutcomeDetail::create(...)
            )
        );
    }


    /** @param OutcomeDetail[] $objectives */
    public function __construct(
        public string $id,
        public array $objectives = []
    ) {
    }


    public function merge(OutcomeRegistry $outcomeRegistry): bool
    {
        try {
            return $this->mergeOutcomes(
                $this->objectives,
                $outcomeRegistry->objectives
            );
        } catch (\Throwable $t) {
            throw new \RuntimeException(
                sprintf("Error merging %s into %s", $this->id, $outcomeRegistry->id),
                0,
                $t
            );
        }
    }


    public function equals(OutcomeRegistry $outcomeRegistry): bool
    {
        return $this->compareOutcomes(
            $this->objectives,
            $outcomeRegistry->objectives
        );
    }


    /**
     * @param OutcomeDetail[] $target
     * @param OutcomeDetail[] $source
     * @return bool
     */
    private function mergeOutcomes(
        array &$target,
        array &$source
    ): bool {
        $updated = false;

        $this->indexOutcomes($target, $source);

        foreach ($source as $id => $sourceOutcome) {
            // Look for source outcome in target
            $targetOutcome = $target[$id] ?? null;

            if ($targetOutcome === null) {
                // If source does not exist in target, add to target
                $target[$id] = $sourceOutcome;
                $updated = true;
            } else {
                // If source does exist in target, merge sources's children into target's children
                $_updated = $this->mergeOutcomes(
                    $sourceOutcome->children,
                    $targetOutcome->children
                );

                if ($_updated) {
                    $updated = true;
                }
            }
        }

        $target = array_values($target);

        return $updated;
    }


    /**
     * @param OutcomeDetail[] $target
     * @param OutcomeDetail[] $source
     * @return bool
     */
    private function compareOutcomes(
        array &$target,
        array &$source
    ): bool {
        $this->indexOutcomes($target, $source);
        if (count($source) !== count($target)) {
            return false;
        }

        foreach ($source as $id => $sourceOutcome) {
            $targetOutcome = $target[$id] ?? null;
            if (
                $targetOutcome === null ||
                !$this->compareOutcomes($sourceOutcome->children, $targetOutcome->children)
            ) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param OutcomeDetail[] $target
     * @param OutcomeDetail[] $source
     * @return void
     */
    private function indexOutcomes(
        array &$target,
        array &$source
    ): void {
        $key = fn(OutcomeDetail $v): array => [$v, $v->id];
        $target = array_column(array_map($key, $target), 0, 1);
        $source = array_column(array_map($key, $source), 0, 1);
    }
}
