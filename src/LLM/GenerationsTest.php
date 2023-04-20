<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Generations::class)]
final class GenerationsTest extends TestCase
{
    private Generations $generations;

    public function setUp(): void
    {
        $this->generations = new Generations(
            new Generation('one'),
            new Generation('two'),
            new Generation('three'),
        );
    }

    public function testToString(): void
    {
        $this->assertSame("one\ntwo\nthree", (string) $this->generations);
    }

    public function testCount(): void
    {
        $this->assertCount(3, $this->generations);
    }

    public function testIterator(): void
    {
        $iteratedGenerations = [];
        foreach ($this->generations as $generation) {
            $iteratedGenerations[] = $generation;
        }

        $this->assertCount(3, $iteratedGenerations);
        $this->assertContainsOnlyInstancesOf(Generation::class, $iteratedGenerations);
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
