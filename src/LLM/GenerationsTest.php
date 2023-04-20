<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Generations::class)]
final class GenerationsTest extends TestCase
{
    public function testCount(): void
    {
        $generation1 = new Generation('one');
        $generation2 = new Generation('two');
        $generation3 = new Generation('three');

        $generations = new Generations($generation1, $generation2, $generation3);

        $this->assertCount(3, $generations);
    }

    public function testIterator(): void
    {
        $generation1 = new Generation('one');
        $generation2 = new Generation('two');
        $generation3 = new Generation('three');

        $generations = new Generations($generation1, $generation2, $generation3);

        $iteratedGenerations = [];
        foreach ($generations as $generation) {
            $iteratedGenerations[] = $generation;
        }

        $this->assertSame([$generation1, $generation2, $generation3], $iteratedGenerations);
    }

    public function testArrayAccess(): void
    {
        $generations = new Generations();
        $generations[] = new Generation('one');
        $generations[] = new Generation('two');
        $generations[] = new Generation('three');

        $generations[1] = new Generation('four');
        unset($generations[2]);

        $this->assertEquals(new Generation('one'), $generations[0]);
        $this->assertEquals(new Generation('four'), $generations[1]);
        $this->assertArrayNotHasKey(2, $generations);
    }
}
