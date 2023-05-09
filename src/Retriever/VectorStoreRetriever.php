<?php

declare(strict_types=1);

namespace Vexo\Retriever;

use Vexo\Contract\Document\Documents;
use Vexo\VectorStore\VectorStoreSearcher;

final class VectorStoreRetriever implements Retriever
{
    public function __construct(
        private readonly VectorStoreSearcher $vectorStore,
        private readonly int $numResults = 4
    ) {
    }

    public function retrieve(string $query): Documents
    {
        return $this->vectorStore->search($query, $this->numResults);
    }
}
