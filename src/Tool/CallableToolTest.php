<?php

declare(strict_types=1);

namespace Vexo\Tool;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CallableTool::class)]
final class CallableToolTest extends TestCase
{
    public function testRun(): void
    {
        $tool = new CallableTool('google', 'Useful for search', fn ($input) => 'I received: ' . $input);

        $this->assertEquals('google', $tool->name());
        $this->assertEquals('Useful for search', $tool->description());
        $this->assertEquals('I received: Best restaurants in Amsterdam', $tool->run('Best restaurants in Amsterdam'));
    }
}
