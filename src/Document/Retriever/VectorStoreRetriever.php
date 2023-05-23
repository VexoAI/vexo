<?php

declare(strict_types=1);

namespace Vexo\Document\Retriever;

use Vexo\Document\Documents;
use Vexo\VectorStore\VectorStore;

final class VectorStoreRetriever implements Retriever
{
    public function __construct(
        private readonly VectorStore $vectorStore,
    ) {
    }

    public function retrieve(string $query, int $maxResults = 4): Documents
    {
        return $this->vectorStore->similaritySearch($query, $maxResults);
    }
}
