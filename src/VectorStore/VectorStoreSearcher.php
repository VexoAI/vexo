<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Documents;

interface VectorStoreSearcher
{
    public function search(string $query, int $numResults = 1): Documents;
}
