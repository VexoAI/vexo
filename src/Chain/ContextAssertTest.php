<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContextAssert::class)]
final class ContextAssertTest extends TestCase
{
    public function testCorrectExceptionIsUsed(): void
    {
        $this->expectException(FailedToValidateContextValue::class);

        ContextAssert::keyExists(['foo' => 'bar'], 'fudge');
    }
}
