<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain\Retriever;

use Vexo\Contract\Document\Documents;
use Vexo\VectorStore\SearchableVectorStore;

final class VectorStoreRetriever implements Retriever
{
    public function __construct(
        private readonly SearchableVectorStore $vectorStore,
        private readonly int $numResults = 4
    ) {
    }

    public function retrieve(string $query): Documents
    {
        return $this->vectorStore->search($query, $this->numResults);
    }
}
