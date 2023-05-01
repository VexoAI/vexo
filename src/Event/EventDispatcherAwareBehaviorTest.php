<?php

declare(strict_types=1);

namespace Vexo\Event;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventDispatcherAwareBehavior::class)]
final class EventDispatcherAwareBehaviorTest extends TestCase
{
    public function testDefaultEventDispatcherGetsInstantiated(): void
    {
        $eventDispatcherAware = new class() {
            use EventDispatcherAwareBehavior;
        };

        $eventDispatcher = $eventDispatcherAware->eventDispatcher();

        $this->assertInstanceOf(EventDispatcher::class, $eventDispatcher);
    }

    public function testEventDispatcherCanBeInjected(): void
    {
        $eventDispatcherAware = new class() {
            use EventDispatcherAwareBehavior;
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcherAware->useEventDispatcher($eventDispatcher);

        $this->assertSame($eventDispatcher, $eventDispatcherAware->eventDispatcher());
    }

    public function testEventIsEmitted(): void
    {
        /**
         * @var BaseEvent[] $emittedEvents
         */
        $emittedEvents = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            SomethingHappened::class,
            function (SomethingHappened $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );

        $eventDispatcherAware = new class() {
            use EventDispatcherAwareBehavior;

            public function emitEvent(object $event): void
            {
                $this->emit($event);
            }
        };
        $eventDispatcherAware->useEventDispatcher($eventDispatcher);

        $event = new class() extends BaseEvent {};
        $eventDispatcherAware->emitEvent($event);

        $this->assertCount(1, $emittedEvents);
        $this->assertSame($event, $emittedEvents[0]);
        $this->assertEquals($eventDispatcherAware::class, $emittedEvents[0]->emitter);
    }
}
