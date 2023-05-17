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
        $exception = FailedToGetContextValue::with('fudge', ['foo', 'baz']);

        $this->assertEquals(
            'Failed to get context value "fudge". Available values: foo, baz',
            $exception->getMessage()
        );
    }
}
