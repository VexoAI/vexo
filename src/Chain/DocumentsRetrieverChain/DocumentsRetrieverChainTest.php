<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\FailedToValidateContextValue;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Document\Repository\Repository;

#[CoversClass(DocumentsRetrieverChain::class)]
final class DocumentsRetrieverChainTest extends TestCase
{
    private Repository $repository;

    private DocumentsRetrieverChain $chain;

    protected function setUp(): void
    {
        $this->repository = new class() implements Repository {
            public function __construct(
                private readonly Documents $documents = new Documents()
            ) {
            }

            public function persist(Document $document): void
            {
                $this->documents->add($document);
            }

            public function search(string $query, int $maxResults = 4): Documents
            {
                return $this->documents;
            }
        };

        $this->chain = new DocumentsRetrieverChain($this->repository);
    }

    public function testRun(): void
    {
        $this->repository->persist(new Document('My amazing content', new Metadata(['id' => 1])));
        $this->repository->persist(new Document('My amazing content', new Metadata(['id' => 2])));

        $context = new Context(['query' => 'Something amazing']);

        $this->chain->run($context);

        $this->assertEquals(1, $context->get('documents')[0]->metadata()->get('id'));
        $this->assertEquals(2, $context->get('documents')[1]->metadata()->get('id'));
    }

    public function testRunWithInvalidContext(): void
    {
        $context = new Context(['query' => '']);

        $this->expectException(FailedToValidateContextValue::class);
        $this->chain->run($context);
    }
}
