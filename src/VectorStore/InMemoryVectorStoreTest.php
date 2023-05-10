<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\EmbeddingModel\FakeModel;

#[CoversClass(InMemoryVectorStore::class)]
#[IgnoreClassForCodeCoverage(DocumentAdded::class)]
final class InMemoryVectorStoreTest extends TestCase
{
    private FakeModel $fakeModel;

    private InMemoryVectorStore $vectorStore;

    protected function setUp(): void
    {
        $this->fakeModel = new FakeModel();

        $this->vectorStore = new InMemoryVectorStore(
            embeddingModel: $this->fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );

        for ($i = 0; $i < 100; $i++) {
            $this->fakeModel->addVector(new Vector([$this->generateFloat(), $this->generateFloat(), $this->generateFloat()]));
            $this->vectorStore->add(new Document('My amazing content ' . $i, new Metadata(['id' => $i])));
        }
    }

    public function testSearch(): void
    {
        $this->fakeModel->addVector(new Vector([0.5, -0.5, 0.25]));
        $results = $this->vectorStore->search('Something amazing', 3);

        $this->assertCount(3, $results);

        $this->assertEquals(10, $results[0]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94721, $results[0]->metadata()->get('score'), 0.00005);

        $this->assertEquals(34, $results[1]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94703, $results[1]->metadata()->get('score'), 0.00005);

        $this->assertEquals(58, $results[2]->metadata()->get('id'));
        $this->assertEqualsWithDelta(0.94684, $results[2]->metadata()->get('score'), 0.00005);
    }

    public function testPersistToFile(): void
    {
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $this->vectorStore->persistToFile($filesystem, 'test.serialized');

        $serialized = $filesystem->read('test.serialized');
        [$hashBuckets, $hyperplanes] = unserialize($serialized);

        $this->assertCount(2, $hashBuckets);
        $this->assertCount(49, $hashBuckets['0']);
        $this->assertCount(51, $hashBuckets['1']);

        $this->assertCount(1, $hyperplanes);
        $this->assertInstanceOf(Vectors::class, $hyperplanes);
    }

    public function testRestoreFromFile(): void
    {
        $filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $this->vectorStore->persistToFile($filesystem, 'test.serialized');

        $newVectorStore = new InMemoryVectorStore(
            embeddingModel: $this->fakeModel,
            numDimensions: 3,
            numHyperplanes: 1,
            randomDimensionGenerator: fn (): float => $this->generateFloat()
        );
        $newVectorStore->restoreFromFile($filesystem, 'test.serialized');

        $this->fakeModel->addVector(new Vector([$this->generateFloat(), $this->generateFloat(), $this->generateFloat()]));
        $results = $newVectorStore->search('Something amazing', 3);

        $this->assertCount(3, $results);
    }

    /**
     * Deterministic random number generator.
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
