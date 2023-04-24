<?php

declare(strict_types=1);

namespace Vexo\Tool;

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
}
