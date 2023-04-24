<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Action::class)]
final class ActionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $action = new Action('google', 'Best restaurants in Amsterdam');

        $this->assertEquals('google', $action->tool());
        $this->assertEquals('Best restaurants in Amsterdam', $action->input());
    }
}
