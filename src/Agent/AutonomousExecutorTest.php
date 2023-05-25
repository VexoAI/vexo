<?php

declare(strict_types=1);

namespace Vexo\Agent;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Contract\Event;

#[CoversClass(AutonomousExecutor::class)]
final class AutonomousExecutorTest extends TestCase
{
    public function testRunToCompletion(): void
    {
        $emittedEvents = [];
        $agent = new StubAgent();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $executor = new AutonomousExecutor(agent: $agent, eventDispatcher: $eventDispatcher);

        $context = new Context();
        $executor->run($context);

        $this->assertInstanceOf(Steps::class, $context->get('steps_taken'));
        $this->assertCount(2, $context->get('steps_taken'));

        $this->assertInstanceOf(Conclusion::class, $context->get('conclusion'));
        $this->assertEquals('Final thought', $context->get('conclusion')->thought());
        $this->assertEquals('Final observation', $context->get('conclusion')->observation());

        $this->assertCount(3, $emittedEvents);
        $this->assertInstanceOf(ExecutorCompletedIteration::class, $emittedEvents[0]);
        $this->assertInstanceOf(ExecutorCompletedIteration::class, $emittedEvents[1]); // @phpstan-ignore-line
        $this->assertInstanceOf(ExecutorCompletedExecution::class, $emittedEvents[2]);
    }

    public function testRunToMaxIterations(): void
    {
        $emittedEvents = [];
        $agent = new StubAgent();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $executor = new AutonomousExecutor(agent: $agent, maxIterations: 1, eventDispatcher: $eventDispatcher);

        $context = new Context();
        $executor->run($context);

        $this->assertInstanceOf(Steps::class, $context->get('steps_taken'));
        $this->assertCount(1, $context->get('steps_taken'));

        $this->assertInstanceOf(Conclusion::class, $context->get('conclusion'));
        $this->assertEquals('Max iterations or time reached. Aborting.', $context->get('conclusion')->thought());
        $this->assertEquals('Could not complete all steps to reach a conclusion.', $context->get('conclusion')->observation());

        $this->assertCount(2, $emittedEvents);
        $this->assertInstanceOf(ExecutorCompletedIteration::class, $emittedEvents[0]);
        $this->assertInstanceOf(ExecutorAbortedAgent::class, $emittedEvents[1]); // @phpstan-ignore-line
    }

    public function testRunToMaxTime(): void
    {
        $emittedEvents = [];
        $agent = new StubAgent();
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $executor = new AutonomousExecutor(agent: $agent, maxTime: 0, eventDispatcher: $eventDispatcher);

        $context = new Context();
        $executor->run($context);

        $this->assertInstanceOf(Steps::class, $context->get('steps_taken'));
        $this->assertCount(1, $context->get('steps_taken'));

        $this->assertInstanceOf(Conclusion::class, $context->get('conclusion'));
        $this->assertEquals('Max iterations or time reached. Aborting.', $context->get('conclusion')->thought());
        $this->assertEquals('Could not complete all steps to reach a conclusion.', $context->get('conclusion')->observation());

        $this->assertCount(2, $emittedEvents);
        $this->assertInstanceOf(ExecutorCompletedIteration::class, $emittedEvents[0]);
        $this->assertInstanceOf(ExecutorAbortedAgent::class, $emittedEvents[1]); // @phpstan-ignore-line
    }
}

final class StubAgent implements Agent
{
    public function planNextStep(Context $context, Steps $previousSteps): Step|Conclusion
    {
        if ($previousSteps->count() == 2) {
            return new Conclusion('Final thought', 'Final observation');
        }

        return new Step(
            'Some thought ' . $previousSteps->count(),
            'Some action ' . $previousSteps->count(),
            'Some input ' . $previousSteps->count()
        );
    }

    public function takeStep(Context $context, Steps $previousSteps, Step $step): Step
    {
        return $step->withObservation(
            'Some observation ' . $previousSteps->count()
        );
    }
}
