<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SorryNotAllRequiredValuesWereGiven::class)]
final class SorryNotAllRequiredValuesWereGivenTest extends TestCase
{
    public function testWithRequiredValues(): void
    {
        $exception = SorryNotAllRequiredValuesWereGiven::with(['foo', 'bar']);

        $this->assertEquals(
            'Sorry, not all required values were given. Missing: foo, bar',
            $exception->getMessage()
        );
    }
}
