<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChainFinished::class)]
final class ChainFinishedTest extends TestCase
{
    public function testGetters(): void
    {
        $context = new Context(['foo' => 'bar']);
        $event = new ChainFinished(
            chainIdentifier: '1234',
            chainClass: 'MyChain',
            context: $context
        );

        $this->assertEquals('1234', $event->chainIdentifier());
        $this->assertEquals('MyChain', $event->chainClass());
        $this->assertSame($context, $event->context());
    }
}
