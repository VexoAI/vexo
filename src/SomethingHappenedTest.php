<?php

declare(strict_types=1);

namespace Vexo;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SomethingHappened::class)]
final class SomethingHappenedTest extends TestCase
{
    public function testPayload(): void
    {
        $somethingHappened = (new StubSomethingHappened('foo', 42))->for($this);

        $this->assertSame(
            ['emitter' => SomethingHappenedTest::class, 'foo' => 'foo', 'bar' => 42],
            $somethingHappened->payload()
        );
    }
}

class StubSomethingHappened extends SomethingHappened
{
    public function __construct(
        public string $foo,
        public int $bar
    ) {
    }
}
