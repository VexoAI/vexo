<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Input;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Retriever\CallbackRetriever;

#[CoversClass(DocumentsRetrieverChain::class)]
final class DocumentsRetrieverChainTest extends TestCase
{
    public function testProcess(): void
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

        $input = new Input(['query' => 'Something amazing']);

        $output = $chain->process($input);

        $this->assertSame($documents, $output->get('documents'));
    }

    public function testInputKeys(): void
    {
        $chain = new DocumentsRetrieverChain(
            retriever: new CallbackRetriever(fn (string $query): DocumentsContract => new Documents()),
            inputKey: 'foo'
        );

        $this->assertSame(['foo'], $chain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $chain = new DocumentsRetrieverChain(
            retriever: new CallbackRetriever(fn (string $query): DocumentsContract => new Documents()),
            outputKey: 'bar'
        );

        $this->assertSame(['bar'], $chain->outputKeys());
    }
}
