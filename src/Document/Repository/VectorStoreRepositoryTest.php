<?php

declare(strict_types=1);

namespace Vexo\Document\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;
use Vexo\Document\Document;
use Vexo\Model\Embedding\FakeModel;
use Vexo\VectorStore\FakeVectorStore;

#[CoversClass(VectorStoreRepository::class)]
final class VectorStoreRepositoryTest extends TestCase
{
    private FakeModel $fakeModel;

    private FakeVectorStore $fakeVectorStore;

    private VectorStoreRepository $repository;

    protected function setUp(): void
    {
        $this->fakeModel = new FakeModel();
        $this->fakeVectorStore = new FakeVectorStore();
        $this->repository = new VectorStoreRepository(
            $this->fakeModel,
            $this->fakeVectorStore,
            ['title']
        );
    }

    public function testPersistAndSearch(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->fakeModel->addVector(new Vector([0.1, -0.1]));
            $this->repository->persist(new Document('some contents ', new Metadata(['title' => 'Title ' . $i])));
        }

        $documents = $this->repository->search('some query', maxResults: 4);

        $this->assertCount(4, $documents);
        for ($i = 0; $i < 4; $i++) {
            $this->assertEquals('some contents ', $documents[$i]->contents());
            $this->assertEquals('Title ' . $i, $documents[$i]->metadata()->get('title'));
        }
    }
}
