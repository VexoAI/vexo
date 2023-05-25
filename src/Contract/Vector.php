<?php

declare(strict_types=1);

namespace Vexo\Contract;

final class Vector
{
    /**
     * @param array<int, float> $vector
     */
    public function __construct(
        private readonly array $vector
    ) {
    }

    /**
     * @return array<int, float>
     */
    public function toArray(): array
    {
        return $this->vector;
    }

    public function dimensions(): int
    {
        return \count($this->vector);
    }

    public function distance(self $other, DistanceAlgorithm $algorithm): float
    {
        return ($algorithm->value)($this->vector, $other->toArray());
    }

    public function similarity(self $other, SimilarityAlgorithm $algorithm): float
    {
        return ($algorithm->value)($this->vector, $other->toArray());
    }
}
