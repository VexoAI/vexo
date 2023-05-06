<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Vector\Vector;

interface VectorStoreReader
{
    public function search(Vector $query, int $numResults = 1): SearchResults;
}
