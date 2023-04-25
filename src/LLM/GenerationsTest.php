<?php

declare(strict_types=1);

namespace Vexo\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Generations::class)]
final class GenerationsTest extends TestCase
{
    public function testGetType(): void
    {
        $generations = new Generations();

        $this->assertSame(Generation::class, $generations->getType());
    }

    public function testFromString(): void
    {
        $generations = Generations::fromString('My text');

        $this->assertTrue($generations->contains(new Generation('My text'), false));
    }

    public function testToString(): void
    {
        $generations = new Generations([new Generation('one'), new Generation('two')]);

        $this->assertSame("one\ntwo", (string) $generations);
    }
}
