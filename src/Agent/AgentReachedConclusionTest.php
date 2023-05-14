<?php

declare(strict_types=1);

namespace Vexo\Agent;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;

#[CoversClass(AgentReachedConclusion::class)]
final class AgentReachedConclusionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $context = new Context();
        $previousSteps = new Steps();
        $conclusion = new Conclusion(thought: 'Some thought', observation: 'Some observation');
        $event = new AgentReachedConclusion($context, $previousSteps, $conclusion);

        $this->assertSame($context, $event->context());
        $this->assertSame($previousSteps, $event->previousSteps());
        $this->assertSame($conclusion, $event->conclusion());
    }
}
