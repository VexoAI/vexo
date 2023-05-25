<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\FailedToValidateContextValue;
use Vexo\Contract\Metadata;
use Vexo\Document\Document;
use Vexo\Document\Documents;
use Vexo\Document\Retriever\Retriever;

#[CoversClass(DocumentsRetrieverChain::class)]
final class DocumentsRetrieverChainTest extends TestCase
{
    private Retriever $retriever;

    private DocumentsRetrieverChain $chain;

    protected function setUp(): void
    {
        $this->retriever = new class() implements Retriever {
            public function __construct(
                private readonly Documents $documents = new Documents()
            ) {
            }

            public function persist(Document $document): void
            {
                $this->documents->add($document);
            }

            public function retrieve(string $query, int $maxResults = 4): Documents
            {
                return $this->documents;
            }
        };

        $this->chain = new DocumentsRetrieverChain($this->retriever, 2);
    }

    public function testRun(): void
    {
        $this->retriever->persist(new Document('My amazing content', new Metadata(['id' => 1])));
        $this->retriever->persist(new Document('My amazing content', new Metadata(['id' => 2])));

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
