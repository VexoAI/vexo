<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use PHPUnit\Framework\TestCase;

final class GenerationTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $text = 'text';
        $generation = new Generation($text);

        $this->assertSame($text, $generation->text());
    }
}
