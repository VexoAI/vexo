<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector;

interface Vector
{
    /**
     * @return array<int, float>
     */
    public function toArray(): array;

    public function distance(self $other, DistanceAlgorithm $algorithm): float;

    public function similarity(self $other, SimilarityAlgorithm $algorithm): float;
}
