<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector\Implementation;

use Vexo\Compare\Distance;
use Vexo\Compare\Similarity;
use Vexo\Contract\Vector\DistanceAlgorithm;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector as VectorContract;

final class Vector implements VectorContract
{
    public function __construct(
        private readonly array $vector
    ) {
    }

    /**
     * @param array<int, float> $vector
     */
    public static function fromArray(array $vector): static
    {
        return new static($vector);
    }

    /**
     * @return array<int, float>
     */
    public function toArray(): array
    {
        return $this->vector;
    }

    public function distance(VectorContract $other, DistanceAlgorithm $algorithm): float
    {
        return Distance::{$algorithm->value}($this->vector, $other->toArray());
    }

    public function similarity(VectorContract $other, SimilarityAlgorithm $algorithm): float
    {
        return Similarity::{$algorithm->value}($this->vector, $other->toArray());
    }
}
