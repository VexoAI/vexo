<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToFindRequiredContextValueForChain::class)]
final class FailedToFindRequiredContextValueForChainTest extends TestCase
{
    public function testWith(): void
    {
        $exception = FailedToFindRequiredContextValueForChain::with(
            'some-variable',
            'MyChain',
            '1234'
        );

        $this->assertStringContainsString(
            'value "some-variable" for chain MyChain (1234)',
            $exception->getMessage()
        );
    }
}
