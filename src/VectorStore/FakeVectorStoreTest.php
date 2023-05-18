<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;

#[CoversClass(FakeVectorStore::class)]
final class FakeVectorStoreTest extends TestCase
{
    public function testSearch(): void
    {
        $vectorStore = new FakeVectorStore();

        $vectorStore->add(new Vector([0.1, -0.1]), new Metadata(['id' => 1]));
        $vectorStore->add(new Vector([0.1, -0.1]), new Metadata(['id' => 2]));
        $vectorStore->add(new Vector([0.1, -0.1]), new Metadata(['id' => 3]));

        $results = $vectorStore->search('Something amazing', maxResults: 1);

        $this->assertCount(1, $results);

        $this->assertEquals(new Vector([0.1, -0.1]), $results[0]->vector());
        $this->assertEquals(1, $results[0]->metadata()->get('id'));
        $this->assertEquals(0.5, $results[0]->score());
    }
}
