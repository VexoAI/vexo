<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Metadata\Implementation\Metadata;

#[CoversClass(FakeVectorStore::class)]
final class FakeVectorStoreTest extends TestCase
{
    public function testSearch(): void
    {
        $vectorStore = new FakeVectorStore();

        $vectorStore->add(new Document('My amazing content', new Metadata(['id' => 1])));

        $results = $vectorStore->search('Something amazing');

        $this->assertCount(1, $results);

        $this->assertEquals(1, $results[0]->metadata()->get('id'));
        $this->assertEquals(1.0, $results[0]->metadata()->get('score'));
    }
}
