<?php

declare(strict_types=1);

namespace Vexo\Agent;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

#[CoversClass(ReasonAndActAgent::class)]
final class ReasonAndActAgentTest extends TestCase
{
    public function testPlanNextStep(): void
    {
        $emittedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $languageModelChain = new FakeChain([
            'thought' => 'I should google for the weather',
            'action' => 'google',
            'input' => 'weather in Amsterdam'
        ]);
        $agent = new ReasonAndActAgent($languageModelChain, new Tools(), $eventDispatcher);

        $context = new Context(['question' => 'What is the weather in Amsterdam?']);
        $previousSteps = new Steps();

        $nextStep = $agent->planNextStep($context, $previousSteps);

        $this->assertInstanceOf(Step::class, $nextStep);
        $this->assertEquals('I should google for the weather', $nextStep->thought());
        $this->assertEquals('google', $nextStep->action());
        $this->assertEquals('weather in Amsterdam', $nextStep->input());
        $this->assertNull($nextStep->observation());

        $this->assertCount(1, $emittedEvents);
        $this->assertInstanceOf(AgentPlannedNextStep::class, $emittedEvents[0]);
    }

    public function testPlanNextStepCreatesConclusion(): void
    {
        $emittedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $languageModelChain = new FakeChain([
            'final_thought' => 'I have found the answer',
            'final_answer' => 'It is cloudy and 21 degrees'
        ]);
        $agent = new ReasonAndActAgent($languageModelChain, new Tools(), $eventDispatcher);

        $context = new Context(['question' => 'What is the weather in Amsterdam?']);
        $previousSteps = new Steps();

        $conclusion = $agent->planNextStep($context, $previousSteps);

        $this->assertInstanceOf(Conclusion::class, $conclusion);
        $this->assertEquals('I have found the answer', $conclusion->thought());
        $this->assertEquals('It is cloudy and 21 degrees', $conclusion->observation());

        $this->assertCount(1, $emittedEvents);
        $this->assertInstanceOf(AgentReachedConclusion::class, $emittedEvents[0]);
    }

    public function testTakeStep(): void
    {
        $emittedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );
        $tools = new Tools([
            new Callback('google', 'Useful for search', fn ($input): string => 'It is cloudy and 21 degrees')
        ]);
        $agent = new ReasonAndActAgent(new FakeChain(), $tools, $eventDispatcher);

        $context = new Context(['question' => 'What is the weather in Amsterdam?']);
        $previousSteps = new Steps();
        $nextStep = new Step(
            thought: 'I should google for the weather',
            action: 'google',
            input: 'weather in Amsterdam'
        );

        $completedStep = $agent->takeStep($context, $previousSteps, $nextStep);

        $this->assertEquals('I should google for the weather', $completedStep->thought());
        $this->assertEquals('google', $completedStep->action());
        $this->assertEquals('weather in Amsterdam', $completedStep->input());
        $this->assertEquals('It is cloudy and 21 degrees', $completedStep->observation());

        $this->assertCount(1, $emittedEvents);
        $this->assertInstanceOf(AgentTookStep::class, $emittedEvents[0]);
    }
}

final class FakeChain implements Chain
{
    public function __construct(
        private readonly array $valuesToAddToContext = [],
    ) {
    }

    public function run(Context $context): void
    {
        foreach ($this->valuesToAddToContext as $name => $value) {
            $context->put($name, $value);
        }
    }
}
