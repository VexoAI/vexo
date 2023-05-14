<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(AgentTookStep::class)]
final class AgentTookStepTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $context = new Context();
        $previousSteps = new Steps();
        $completedStep = new Step(thought: 'Some thought', action: 'Some action');
        $event = new AgentTookStep($context, $previousSteps, $completedStep);

        $this->assertSame($context, $event->context());
        $this->assertSame($previousSteps, $event->previousSteps());
        $this->assertSame($completedStep, $event->completedStep());
    }
}
