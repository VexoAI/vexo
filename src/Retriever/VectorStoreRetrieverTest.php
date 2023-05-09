<?php

declare(strict_types=1);

namespace Vexo\Retriever;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\VectorStore\FakeVectorStore;

#[CoversClass(VectorStoreRetriever::class)]
final class VectorStoreRetrieverTest extends TestCase
{
    public function testRetrieve(): void
    {
        $vectorStore = new FakeVectorStore();
        $vectorStore->add(new Document('My amazing content', new Metadata(['id' => 1])));
        $vectorStore->add(new Document('My amazing content', new Metadata(['id' => 2])));

        $retriever = new VectorStoreRetriever(
            vectorStore: $vectorStore,
            numResults: 2
        );

        $results = $retriever->retrieve('Something amazing');

        $this->assertCount(2, $results);

        $this->assertEquals(1, $results[0]->metadata()->get('id'));
        $this->assertEquals(2, $results[1]->metadata()->get('id'));
    }
}
