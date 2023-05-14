<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToResolveTool::class)]
final class FailedToResolveToolTest extends TestCase
{
    public function testFor(): void
    {
        $exception = FailedToResolveTool::for('unknown tool', ['tool1', 'tool2']);

        $this->assertStringContainsString(
            'Failed to resolve tool "unknown tool"; available tools: tool1, tool2',
            $exception->getMessage()
        );
    }
}
