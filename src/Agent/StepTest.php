<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Step::class)]
final class StepTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $action = new Action('google', 'Best restaurants in Amsterdam');
        $step = new Step($action, 'Some log');

        $this->assertSame($action, $step->action());
        $this->assertEquals('Some log', $step->log());
    }

    public function testWithObservation(): void
    {
        $action = new Action('google', 'Best restaurants in Amsterdam');
        $step = new Step($action, 'Some log');

        $this->assertNull($step->observation());

        $step = $step->withObservation('Some observation');
        $this->assertEquals('Some observation', $step->observation());
    }
}
