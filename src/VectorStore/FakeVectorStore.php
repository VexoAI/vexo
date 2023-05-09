<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Document as DocumentContract;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Documents;

final class FakeVectorStore implements WritableVectorStore, SearchableVectorStore
{
    public function __construct(
        private readonly DocumentsContract $documents = new Documents()
    ) {
    }

    public function add(DocumentContract $document): void
    {
        $document->metadata()->offsetSet('score', 1.0);
        $this->documents->add($document);
    }

    public function search(string $query, int $numResults = 4): DocumentsContract
    {
        return $this->documents;
    }
}
