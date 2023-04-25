<?php

declare(strict_types=1);

namespace Vexo\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Generations::class)]
final class GenerationsTest extends TestCase
{
    private Generations $generations;

    protected function setUp(): void
    {
        $this->generations = new Generations([
            new Generation('one'),
            new Generation('two'),
            new Generation('three'),
        ]);
    }

    public function testFromString(): void
    {
        $generations = Generations::fromString('My text');

        $this->assertTrue($generations->contains(new Generation('My text'), false));
    }

    public function testToString(): void
    {
        $this->assertSame("one\ntwo\nthree", (string) $this->generations);
    }
}
