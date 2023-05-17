<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToValidateContextValue::class)]
final class FailedToValidateContextValueTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \InvalidArgumentException('Some exception');
        $failedToValidateContextValue = FailedToValidateContextValue::because('fudge', $exception);

        $this->assertEquals(
            'Failed to validate context value "fudge": Some exception',
            $failedToValidateContextValue->getMessage()
        );
    }
}
