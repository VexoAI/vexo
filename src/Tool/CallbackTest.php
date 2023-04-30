<?php

declare(strict_types=1);

namespace Vexo\Tool;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Callback::class)]
final class CallbackTest extends TestCase
{
    public function testRun(): void
    {
        $tool = new Callback('google', 'Useful for search', fn ($input): string => 'I received: ' . $input);

        $this->assertEquals('google', $tool->name());
        $this->assertEquals('Useful for search', $tool->description());
        $this->assertEquals('I received: Best restaurants in Amsterdam', $tool->run('Best restaurants in Amsterdam'));
    }
}
