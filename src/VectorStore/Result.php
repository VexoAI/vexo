<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;

final class Result
{
    public function __construct(
        private readonly Vector $vector,
        private readonly Metadata $metadata,
        private readonly float $score
    ) {
    }

    public function vector(): Vector
    {
        return $this->vector;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }

    public function score(): float
    {
        return $this->score;
    }
}
