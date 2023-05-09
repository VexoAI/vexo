<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;

#[CoversClass(BaseTool::class)]
#[IgnoreClassForCodeCoverage(ToolStarted::class)]
#[IgnoreClassForCodeCoverage(ToolFinished::class)]
final class BaseToolTest extends TestCase
{
    public function testRun(): void
    {
        $tool = new BaseToolStub('my_tool', 'Useful for things');

        $this->assertEquals('my_tool', $tool->name());
        $this->assertEquals('Useful for things', $tool->description());
        $this->assertEquals('Received: some input', $tool->run('some input'));
    }
}

final class BaseToolStub extends BaseTool
{
    protected function call(string $input): string
    {
        return 'Received: ' . $input;
    }
}
