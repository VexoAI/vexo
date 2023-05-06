<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Ramsey\Collection\AbstractCollection;

final class SearchResults extends AbstractCollection
{
    public function getType(): string
    {
        return SearchResult::class;
    }
}
