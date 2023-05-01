<?php

declare(strict_types=1);

namespace Vexo\Embedding;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Embedding>
 */
final class Embeddings extends AbstractCollection
{
    public function getType(): string
    {
        return Embedding::class;
    }
}
