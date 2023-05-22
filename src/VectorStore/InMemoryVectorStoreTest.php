<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Model\Embedding\FakeModel;

#[CoversClass(InMemoryVectorStore::class)]
#[CoversClass(AddTextsAndDocumentsBehavior::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    public function testSimilaritySearch(): void
    {
        $fakeModel = new FakeModel();

        $vectorStore = new InMemoryVectorStore(
            embeddingModel: $fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );

        $vectorStore->addVectors(
            $this->generateVectors(100, 3),
            $this->generateMetadata(100)
        );

        $fakeModel->addVector($this->generateVector(3));
        $documents = $vectorStore->similaritySearch('Something amazing', 3);

        $this->assertCount(3, $documents);

        $this->assertEquals('Some contents 12', $documents[0]->contents());
        $this->assertEquals(12, $documents[0]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.96969, $documents[0]->metadata()->get('score'), 0.00005);

        $this->assertEquals('Some contents 61', $documents[1]->contents());
        $this->assertEquals(61, $documents[1]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.97642, $documents[1]->metadata()->get('score'), 0.00005);

        $this->assertEquals('Some contents 5', $documents[2]->contents());
        $this->assertEquals(5, $documents[2]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.98767, $documents[2]->metadata()->get('score'), 0.00005);
    }

    public function testAddTexts(): void
    {
        $fakeModel = new FakeModel();

        $vectorStore = new InMemoryVectorStore(
            embeddingModel: $fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );

        for ($i = 0; $i < 100; $i++) {
            $fakeModel->addVector($this->generateVector(3));
        }

        $vectorStore->addTexts(
            array_map(fn (int $i): string => 'Some text ' . $i, range(0, 99)),
            $this->generateMetadata(100)
        );

        $fakeModel->addVector($this->generateVector(3));
        $documents = $vectorStore->similaritySearch('Something amazing', 1);

        $this->assertCount(1, $documents);

        $this->assertEquals('Some text 97', $documents[0]->contents());
        $this->assertEquals(97, $documents[0]->metadata()->get('id'));
    }

    public function testAddDocuments(): void
    {
        $fakeModel = new FakeModel();

        $vectorStore = new InMemoryVectorStore(
            embeddingModel: $fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );

        for ($i = 0; $i < 100; $i++) {
            $fakeModel->addVector($this->generateVector(3));
        }

        $vectorStore->addDocuments(
            new Documents(
                array_map(fn (int $i): Document => new Document('Some text ' . $i, new Metadata(['id' => $i])), range(0, 99))
            )
        );

        $fakeModel->addVector($this->generateVector(3));
        $documents = $vectorStore->similaritySearch('Something amazing', 1);

        $this->assertCount(1, $documents);

        $this->assertEquals('Some text 10', $documents[0]->contents());
        $this->assertEquals(10, $documents[0]->metadata()->get('id'));
    }

    private function generateVectors(int $count, int $numDimensions): Vectors
    {
        return new Vectors(
            array_map(
                fn (): Vector => $this->generateVector($numDimensions),
                range(0, $count - 1)
            )
        );
    }

    private function generateMetadata(int $count): array
    {
        return array_map(
            fn (int $i): Metadata => new Metadata(['id' => $i, 'contents' => 'Some contents ' . $i]),
            range(0, $count - 1)
        );
    }

    private function generateVector(int $numDimensions): Vector
    {
        return new Vector(
            array_map(
                fn (): float => $this->generateFloat(),
                range(0, $numDimensions - 1)
            )
        );
    }

    /**
     * Deterministic "random" number generator.
     */
    private function generateFloat(): float
    {
        static $i = 0;
        mt_srand($i++);

        return mt_rand() / mt_getrandmax() - (mt_rand() / mt_getrandmax());
    }
}
