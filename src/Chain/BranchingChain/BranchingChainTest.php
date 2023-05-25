<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Chain;
use Vexo\Chain\ChainFinished;
use Vexo\Chain\ChainStarted;
use Vexo\Chain\Context;
use Vexo\Contract\Event;

#[CoversClass(BranchingChain::class)]
final class BranchingChainTest extends TestCase
{
    public function testRun(): void
    {
        $chain = new BranchingChain(
            chains: [
                'number > 100' => new FakeChain(['foo' => 'bar']),
                'number <= 100' => new FakeChain(['baz' => 'fudge'])
            ]
        );

        $context = new Context(['number' => 42]);
        $chain->run($context);

        $this->assertSame(
            [
                'number' => 42,
                'baz' => 'fudge'
            ],
            $context->toArray()
        );
    }

    public function testRunThrowsExceptionOnUnknownValue(): void
    {
        $chain = new BranchingChain(
            chains: [
                'nonExistent > 100' => new FakeChain(['foo' => 'bar'])
            ]
        );

        $context = new Context(['number' => 42]);

        $this->expectException(FailedToEvaluateCondition::class);
        $chain->run($context);
    }

    public function testRunEmitsEvents(): void
    {
        $emittedEvents = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );

        $chain = new BranchingChain(
            eventDispatcher: $eventDispatcher,
            chains: [
                'number > 100' => new FakeChain(['foo' => 'bar']),
                'number <= 100' => new FakeChain(['baz' => 'fudge'])
            ]
        );

        $context = new Context(['number' => 42]);
        $chain->run($context);

        $this->assertCount(4, $emittedEvents);
        $this->assertInstanceOf(ChainBranchConditionEvaluated::class, $emittedEvents[0]);
        $this->assertInstanceOf(ChainBranchConditionEvaluated::class, $emittedEvents[1]);
        $this->assertInstanceOf(ChainStarted::class, $emittedEvents[2]);
        $this->assertInstanceOf(ChainFinished::class, $emittedEvents[3]);
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
