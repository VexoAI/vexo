<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(ChainBranchConditionEvaluated::class)]
final class ChainBranchConditionEvaluatedTest extends TestCase
{
    public function testGetters(): void
    {
        $context = new Context(['foo' => 'bar']);
        $event = new ChainBranchConditionEvaluated(
            chainIdentifier: '1234',
            chainClass: 'MyChain',
            context: $context,
            condition: 'number > 100',
            evaluationResult: false
        );

        $this->assertEquals('1234', $event->chainIdentifier());
        $this->assertEquals('MyChain', $event->chainClass());
        $this->assertSame($context, $event->context());
        $this->assertEquals('number > 100', $event->condition());
        $this->assertFalse($event->evaluationResult());
    }
}
