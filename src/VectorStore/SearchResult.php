<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Metadata\Metadata;

final class SearchResult
{
    public function __construct(
        private readonly string $id,
        private readonly float $score,
        private readonly Metadata $metadata
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function score(): float
    {
        return $this->score;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }
}
