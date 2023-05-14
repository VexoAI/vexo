<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(AgentPlannedNextStep::class)]
final class AgentPlannedNextStepTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $context = new Context();
        $previousSteps = new Steps();
        $nextStep = new Step(thought: 'Some thought', action: 'Some action');
        $event = new AgentPlannedNextStep($context, $previousSteps, $nextStep);

        $this->assertSame($context, $event->context());
        $this->assertSame($previousSteps, $event->previousSteps());
        $this->assertSame($nextStep, $event->nextStep());
    }
}
