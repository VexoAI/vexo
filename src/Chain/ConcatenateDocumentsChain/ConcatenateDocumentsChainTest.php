<?php

declare(strict_types=1);

namespace Vexo\Chain\ConcatenateDocumentsChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;

#[CoversClass(ConcatenateDocumentsChain::class)]
final class ConcatenateDocumentsChainTest extends TestCase
{
    public function testRun(): void
    {
        $chain = new ConcatenateDocumentsChain();

        $context = new Context([
            'documents' => new Documents([
                new Document('My first document'),
                new Document('My second document')
            ])
        ]);

        $chain->run($context);

        $this->assertEquals(
            "My first document\n\nMy second document",
            $context->get('combined_contents')
        );
    }
}
