<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\VectorStore\InMemoryVectorStore\BasicLocalitySensitiveHashing;

#[CoversClass(InMemoryVectorStore::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    public function testSearch(): void
    {
        $vectorStore = new InMemoryVectorStore(
            localitySensitiveHashing: new BasicLocalitySensitiveHashing(
                randomDimensionGenerator: fn (): float => 0.5,
                numHyperplanes: 1,
                numDimensions: 3
            )
        );

        for ($i = 0; $i < 100; $i++) {
            $vectorStore->add(
                id: 'id-' . $i,
                vector: new Vector([
                    (100 - $i) / 100,
                    (-100 + $i) / 100,
                    (-50 + $i) / 100
                ]),
                metadata: new Metadata(['title' => 'vector ' . $i])
            );
        }

        $searchResults = $vectorStore->search(new Vector([0.86, -0.5, 0.1]), 3);

        $this->assertCount(3, $searchResults);
        $this->assertEquals('id-56', $searchResults[0]->id());
        $this->assertEquals('id-57', $searchResults[1]->id());
        $this->assertEquals('id-55', $searchResults[2]->id());
    }
}
