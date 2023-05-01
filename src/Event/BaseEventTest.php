<?php

declare(strict_types=1);

namespace Vexo\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BaseEvent::class)]
final class BaseEventTest extends TestCase
{
    public function testPayload(): void
    {
        $somethingHappened = (new StubSomethingHappened('foo', 42))->for($this);

        $this->assertSame(
            ['emitter' => self::class, 'foo' => 'foo', 'bar' => 42],
            $somethingHappened->payload()
        );
    }
}

final class StubSomethingHappened extends BaseEvent
{
    public function __construct(
        public string $foo,
        public int $bar
    ) {
    }
}
