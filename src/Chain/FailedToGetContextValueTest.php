<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToGetContextValue::class)]
final class FailedToGetContextValueTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $context = new Context(['foo' => 'bar', 'baz' => 'qux']);
        $exception = FailedToGetContextValue::with('fudge', $context);

        $this->assertEquals(
            'Failed to get context value "fudge". Available values: foo, baz',
            $exception->getMessage()
        );
    }
}
