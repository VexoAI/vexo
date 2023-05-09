<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\EmbeddingModel\FakeModel;

#[CoversClass(InMemoryVectorStore::class)]
#[IgnoreClassForCodeCoverage(DocumentAdded::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    public function testSearch(): void
    {
        $fakeModel = new FakeModel();

        $vectorStore = new InMemoryVectorStore(
            embeddingModel: $fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );

        for ($i = 0; $i < 100; $i++) {
            $fakeModel->addVector(new Vector([$this->generateFloat(), $this->generateFloat(), $this->generateFloat()]));
            $vectorStore->add(new Document('My amazing content ' . $i, new Metadata(['id' => $i])));
        }

        $fakeModel->addVector(new Vector([0.5, -0.5, 0.25]));
        $results = $vectorStore->search('Something amazing', 3);

        $this->assertCount(3, $results);

        $this->assertEquals(21, $results[0]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94233, $results[0]->metadata()->get('score'), 0.00005);

        $this->assertEquals(45, $results[1]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94210, $results[1]->metadata()->get('score'), 0.00005);

        $this->assertEquals(69, $results[2]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94187, $results[2]->metadata()->get('score'), 0.00005);
    }

    /**
     * Deterministic random number generator.
     */
    private function generateFloat(): float
    {
        static $i = 1;

        $float = ((1_103_515_245 * $i + 12345) & 0x7FFFFFFF) / 0x7FFFFFFF;
        if ($i % 2 === 0) {
            $float = -$float;
        }
        $i++;

        return $float;
    }
}
