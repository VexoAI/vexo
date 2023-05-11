<?php

declare(strict_types=1);

namespace Vexo\LanguageModel\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Prompt::class)]
final class PromptTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $text = 'text';

        $prompt = new Prompt($text);

        $this->assertSame($text, $prompt->text());
    }

    public function testToString(): void
    {
        $text = 'text';

        $prompt = new Prompt($text);

        $this->assertSame($text, (string) $prompt);
    }
}
