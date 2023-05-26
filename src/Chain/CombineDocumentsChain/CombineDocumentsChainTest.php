<?php

declare(strict_types=1);

namespace Vexo\Chain\CombineDocumentsChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\FailedToValidateContextValue;
use Vexo\Document\Document;
use Vexo\Document\Documents;

#[CoversClass(CombineDocumentsChain::class)]
final class CombineDocumentsChainTest extends TestCase
{
    public function testRun(): void
    {
        $chain = new CombineDocumentsChain();

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

    public function testRunWithInvalidContext(): void
    {
        $chain = new CombineDocumentsChain();
        $context = new Context([
            'documents' => 'invalid'
        ]);

        $this->expectException(FailedToValidateContextValue::class);
        $chain->run($context);
    }
}
