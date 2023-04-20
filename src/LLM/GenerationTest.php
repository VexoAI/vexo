<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Generation::class)]
final class GenerationTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $text = 'text';
        $generation = new Generation($text);

        $this->assertSame($text, $generation->text());
    }
}
