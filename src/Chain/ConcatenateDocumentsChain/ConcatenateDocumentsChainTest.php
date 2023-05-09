<?php

declare(strict_types=1);

namespace Vexo\Chain\ConcatenateDocumentsChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Input;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;

#[CoversClass(ConcatenateDocumentsChain::class)]
final class ConcatenateDocumentsChainTest extends TestCase
{
    public function testProcess(): void
    {
        $chain = new ConcatenateDocumentsChain();

        $input = new Input([
            'documents' => new Documents([
                new Document('My first document'),
                new Document('My second document')
            ])
        ]);

        $output = $chain->process($input);

        $this->assertEquals(
            ['text' => "My first document\n\nMy second document"],
            $output->toArray()
        );
    }

    public function testInputKeys(): void
    {
        $chain = new ConcatenateDocumentsChain(inputKey: 'foo');

        $this->assertSame(['foo'], $chain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $chain = new ConcatenateDocumentsChain(outputKey: 'bar');

        $this->assertSame(['bar'], $chain->outputKeys());
    }
}
