<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\TestCase;

final class PromptTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $text = 'text';

        $prompt = new Prompt($text);

        $this->assertSame($text, $prompt->text());
    }
}