<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tools::class)]
final class ToolsTest extends TestCase
{
    public function testGetType(): void
    {
        $tools = new Tools();

        $this->assertSame(Tool::class, $tools->getType());
    }

    public function testResolve(): void
    {
        $tool = new Callback('Foo Tool', 'An amazing tool', fn ($input): string => 'Received: ' . $input);
        $tools = new Tools([$tool]);

        $this->assertSame($tool, $tools->resolve('FOO tool'));
    }

    public function testResolveThrowsException(): void
    {
        $tools = new Tools();

        $this->expectException(FailedToResolveTool::class);
        $tools->resolve('foo');
    }
}
