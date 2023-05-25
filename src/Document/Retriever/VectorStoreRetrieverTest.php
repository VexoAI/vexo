<?php

declare(strict_types=1);

namespace Vexo\Document\Retriever;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata;
use Vexo\Contract\Vectors;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\VectorStore\VectorStore;

#[CoversClass(VectorStoreRetriever::class)]
final class VectorStoreRetrieverTest extends TestCase
{
    public function testRetrieve(): void
    {
        $retriever = new VectorStoreRetriever(
            vectorStore: new FakeVectorStore()
        );

        $documents = $retriever->retrieve('some query', maxResults: 2);

        $this->assertCount(2, $documents);
    }
}

final class FakeVectorStore implements VectorStore
{
    public function similaritySearch(string $query, int $maxResults = 4, bool $includeScoresInMetadata = true): Documents
    {
        return new Documents([
            new Document('some contents ', new Metadata(['title' => 'Title 1'])),
            new Document('some contents ', new Metadata(['title' => 'Title 2']))
        ]);
    }

    public function addVectors(Vectors $vectors, array $metadatas): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function addTexts(array $texts, array $metadatas): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function addDocuments(Documents $documents): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
