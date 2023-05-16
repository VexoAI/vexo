<?php

declare(strict_types=1);

namespace Vexo\Chain;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Event\Event;

#[CoversClass(SequentialRunner::class)]
final class SequentialRunnerTest extends TestCase
{
    public function testRun(): void
    {
        $runner = new SequentialRunner(chains: [
            new FakeChain(['foo' => 'bar']),
            new FakeChain(['baz' => 'fudge'])
        ]);

        $context = new Context(['some-variable' => 'fudge']);
        $runner->run($context);

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

        $runner = new SequentialRunner(
            chains: [new FakeChain(['foo' => 'bar'])],
            eventDispatcher: $eventDispatcher
        );

        $context = new Context(['some-variable' => 'fudge']);
        $runner->run($context);

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
