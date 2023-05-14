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
        $step = new Step('Some thought', 'Some action', 'Some input');

        $this->assertEquals('Some thought', $step->thought());
        $this->assertEquals('Some action', $step->action());
        $this->assertEquals('Some input', $step->input());
    }

    public function testWithObservation(): void
    {
        $step = new Step('Some thought', 'Some action', 'Some input');
        $stepWithObservation = $step->withObservation('Some observation');

        $this->assertEquals('Some thought', $stepWithObservation->thought());
        $this->assertEquals('Some action', $stepWithObservation->action());
        $this->assertEquals('Some input', $stepWithObservation->input());
        $this->assertEquals('Some observation', $stepWithObservation->observation());
    }
}
