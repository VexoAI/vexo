<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Result>
 */
final class Results extends AbstractCollection
{
    public function getType(): string
    {
        return Result::class;
    }
}
