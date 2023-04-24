<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Finish::class)]
final class FinishTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $finish = new Finish(['reason' => 'Task completed successfully']);

        $this->assertEquals(['reason' => 'Task completed successfully'], $finish->results());
    }
}
