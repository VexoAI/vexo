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
    private InMemoryVectorStore $vectorStore;

    protected function setUp(): void
    {
        $this->vectorStore = new InMemoryVectorStore(
            embeddingModel: new FakeModel([
                new Vector([-0.86735673894517, 0.77182569530412]),
                new Vector([0.91102167866706, 0.72931519138129]),
                new Vector([0.31876312257664, -0.49097725445916]),
                new Vector([-0.95490822007643, -0.26773092768515]),
                new Vector([0.21046774983894, -0.57525879590551]),
                new Vector([-0.10722856740804, 0.5842314779685])
            ]),
            numDimensions: 2,
            numHyperplanes: 1
        );
    }

    public function testSimilaritySearch(): void
    {
        $this->vectorStore->addVectors(
            new Vectors([
                new Vector([-0.86735673894517, 0.77182569530412]),
                new Vector([0.91102167866706, 0.72931519138129]),
                new Vector([0.31876312257664, -0.49097725445916]),
                new Vector([-0.95490822007643, -0.26773092768515]),
                new Vector([0.21046774983894, -0.57525879590551])
            ]),
            [
                new Metadata(['id' => 1, 'contents' => 'Some contents 1']),
                new Metadata(['id' => 2, 'contents' => 'Some contents 2']),
                new Metadata(['id' => 3, 'contents' => 'Some contents 3']),
                new Metadata(['id' => 4, 'contents' => 'Some contents 4']),
                new Metadata(['id' => 5, 'contents' => 'Some contents 5'])
            ]
        );

        $documents = $this->vectorStore->similaritySearch('Something amazing', 2);

        $this->assertCount(2, $documents);

        $this->assertEquals('Some contents 1', $documents[0]->contents());
        $this->assertEquals(1, $documents[0]->metadata()->get('id'));
        $this->assertEqualsWithDelta(1.0, $documents[0]->metadata()->get('score'), 0.00005);

        $this->assertEquals('Some contents 4', $documents[1]->contents());
        $this->assertEquals(4, $documents[1]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.53984, $documents[1]->metadata()->get('score'), 0.00005);
    }

    public function testAddTexts(): void
    {
        $this->vectorStore->addTexts(
            [
                'Some text 1',
                'Some text 2',
                'Some text 3',
                'Some text 4',
                'Some text 5'
            ],
            [
                new Metadata(['id' => 1]),
                new Metadata(['id' => 2]),
                new Metadata(['id' => 3]),
                new Metadata(['id' => 4]),
                new Metadata(['id' => 5])
            ]
        );

        $documents = $this->vectorStore->similaritySearch('Something amazing', 1);

        $this->assertCount(1, $documents);

        $this->assertEquals('Some text 1', $documents[0]->contents());
        $this->assertEquals(1, $documents[0]->metadata()->get('id'));
    }

    public function testAddDocuments(): void
    {
        $this->vectorStore->addDocuments(
            new Documents([
                new Document('Some text 1', new Metadata(['id' => 1])),
                new Document('Some text 2', new Metadata(['id' => 2])),
                new Document('Some text 3', new Metadata(['id' => 3])),
                new Document('Some text 4', new Metadata(['id' => 4])),
                new Document('Some text 5', new Metadata(['id' => 5]))
            ])
        );

        $documents = $this->vectorStore->similaritySearch('Something amazing', 1);

        $this->assertCount(1, $documents);

        $this->assertEquals('Some text 1', $documents[0]->contents());
        $this->assertEquals(1, $documents[0]->metadata()->get('id'));
    }
}

function random_int(int $min, int $max): int
{
    static $numbers = [
        1109294673,
        833541787,
        1236955672,
        631981979,
        1127310249,
        69677023
    ];

    return array_pop($numbers);
}
