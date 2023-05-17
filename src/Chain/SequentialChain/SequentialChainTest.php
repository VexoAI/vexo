<?php

declare(strict_types=1);

namespace Vexo\Chain\SequentialChain;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Chain;
use Vexo\Chain\ChainFinished;
use Vexo\Chain\ChainStarted;
use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

#[CoversClass(SequentialChain::class)]
final class SequentialChainTest extends TestCase
{
    public function testRun(): void
    {
        $chain = new SequentialChain(chains: [
            new FakeChain(['foo' => 'bar']),
            new FakeChain(['baz' => 'fudge'])
        ]);

        $context = new Context(['some-variable' => 'fudge']);
        $chain->run($context);

        $this->assertSame(
            [
                'some-variable' => 'fudge',
                'foo' => 'bar',
                'baz' => 'fudge'
            ],
            $context->toArray()
        );
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

        $chain = new SequentialChain(
            chains: [new FakeChain(['foo' => 'bar'])],
            eventDispatcher: $eventDispatcher
        );

        $context = new Context(['some-variable' => 'fudge']);
        $chain->run($context);

        $this->assertCount(2, $emittedEvents);

        $this->assertInstanceOf(ChainStarted::class, $emittedEvents[0]);
        $this->assertInstanceOf(ChainFinished::class, $emittedEvents[1]); // @phpstan-ignore-line
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
