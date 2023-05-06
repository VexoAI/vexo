<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;

#[CoversClass(InMemoryVectorStore::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    public function testSearch(): void
    {
        $vectorStore = new InMemoryVectorStore();

        $vectorStore->add('id-1', new Vector([0.3, -0.7, -0.1]), new Metadata(['title' => 'vector 1']));
        $vectorStore->add('id-2', new Vector([0.4, 0.8, 0.2]), new Metadata(['title' => 'vector 2']));
        $vectorStore->add('id-3', new Vector([-0.5, 0.3, 0.2]), new Metadata(['title' => 'vector 3']));

        $searchResults = $vectorStore->search(new Vector([0.5, 0.6, 0.1]), 3);

        $this->assertEquals('id-2', $searchResults[0]->id());
        $this->assertEquals('id-3', $searchResults[1]->id());
        $this->assertEquals('id-1', $searchResults[2]->id());
    }
}
