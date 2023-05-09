<?php

declare(strict_types=1);

namespace Vexo\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToRenderPrompt::class)]
final class FailedToRenderPromptTest extends TestCase
{
    public function testWithRequiredValues(): void
    {
        $exception = FailedToRenderPrompt::with(['foo', 'bar']);

        $this->assertEquals(
            'Failed to render prompt. Missing values: foo, bar',
            $exception->getMessage()
        );
    }
}
