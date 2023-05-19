<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;
use Vexo\Model\Embedding\FakeModel;

#[CoversClass(InMemoryVectorStore::class)]
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
            $vectorStore->add(
                $this->generateVector(),
                new Metadata(['id' => $i])
            );
        }

        $fakeModel->addVector($this->generateVector());
        $results = $vectorStore->search('Something amazing', 3);

        $this->assertCount(3, $results);

        $this->assertEquals(50, $results[0]->metadata()->get('id'));
        $this->assertEqualsWithDelta(-0.84952, $results[0]->score(), 0.00005);

        $this->assertEquals(74, $results[1]->metadata()->get('id'));
        $this->assertEqualsWithDelta(-0.85029, $results[1]->score(), 0.00005);

        $this->assertEquals(98, $results[2]->metadata()->get('id'));
        $this->assertEqualsWithDelta(-0.85107, $results[2]->score(), 0.00005);
    }

    private function generateVector(): Vector
    {
        return new Vector([$this->generateFloat(), $this->generateFloat(), $this->generateFloat()]);
    }

    /**
     * Deterministic "random" number generator.
     */
    private function generateFloat(): float
    {
        static $i = 0;
        $i++;

        $float = ((1_103_515_245 * $i + 12345) & 0x7FFFFFFF) / 0x7FFFFFFF;
        if (round($float * 10) % 2 === 0) {
            return -$float;
        }

        return $float;
    }
}
