<?php

declare(strict_types=1);

namespace Vexo\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Completions::class)]
final class CompletionsTest extends TestCase
{
    public function testGetType(): void
    {
        $completions = new Completions();

        $this->assertSame(Completion::class, $completions->getType());
    }

    public function testFromString(): void
    {
        $completions = Completions::fromString('My text');

        $this->assertTrue($completions->contains(new Completion('My text'), false));
    }

    public function testToString(): void
    {
        $completions = new Completions([new Completion('one'), new Completion('two')]);

        $this->assertSame("one\ntwo", (string) $completions);
    }
}
