<?php

declare(strict_types=1);

namespace Vexo\Embedding;

final class Embedding
{
    public function __construct(
        private readonly array $embedding
    ) {
    }

    public function toArray(): array
    {
        return $this->embedding;
    }
}
