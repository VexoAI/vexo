<?php

declare(strict_types=1);

namespace Vexo\Chain\ContextValueRemapperChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(ContextValueRemapperChain::class)]
final class ContextValueRemapperChainTest extends TestCase
{
    public function testRun(): void
    {
        $chain = new ContextValueRemapperChain(['query' => 'question']);
        $context = new Context(['query' => 'Something amazing']);

        $chain->run($context);

        $this->assertSame('Something amazing', $context->get('question'));
    }

    public function testRequiredContextValues(): void
    {
        $chain = new ContextValueRemapperChain(['query' => 'question']);

        $this->assertSame(['query' => 'mixed'], $chain->requiredContextValues());
    }
}
