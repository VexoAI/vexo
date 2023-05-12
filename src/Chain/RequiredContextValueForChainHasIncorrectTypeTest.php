<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\InvalidArgumentException;

#[CoversClass(RequiredContextValueForChainHasIncorrectType::class)]
final class RequiredContextValueForChainHasIncorrectTypeTest extends TestCase
{
    public function testWith(): void
    {
        $exception = RequiredContextValueForChainHasIncorrectType::with(
            'some-variable',
            'MyChain',
            '1234',
            new InvalidArgumentException('Some message')
        );

        $this->assertStringContainsString(
            'value "some-variable" for chain MyChain (1234) has incorrect type: Some message',
            $exception->getMessage()
        );
    }
}
