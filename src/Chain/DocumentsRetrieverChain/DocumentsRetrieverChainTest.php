<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Retriever\CallbackRetriever;

#[CoversClass(DocumentsRetrieverChain::class)]
final class DocumentsRetrieverChainTest extends TestCase
{
    public function testRun(): void
    {
        $documents = new Documents([
            new Document('My amazing content', new Metadata(['id' => 1])),
            new Document('My amazing content', new Metadata(['id' => 2])),
        ]);

        $chain = new DocumentsRetrieverChain(
            new CallbackRetriever(
                retrieverFunction: fn (string $query): DocumentsContract => $documents
            )
        );

        $context = new Context(['query' => 'Something amazing']);

        $chain->run($context);

        $this->assertSame($documents, $context->get('documents'));
    }
}
