<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Prompts::class)]
final class PromptsTest extends TestCase
{
    public function testRequiresAtLeastOnePrompt(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Prompts();
    }

    public function testCount(): void
    {
        $prompt1 = new Prompt('one');
        $prompt2 = new Prompt('two');
        $prompt3 = new Prompt('three');

        $prompts = new Prompts($prompt1, $prompt2, $prompt3);

        $this->assertCount(3, $prompts);
    }

    public function testIterator(): void
    {
        $prompt1 = new Prompt('one');
        $prompt2 = new Prompt('two');
        $prompt3 = new Prompt('three');

        $prompts = new Prompts($prompt1, $prompt2, $prompt3);

        $iteratedPrompts = [];
        foreach ($prompts as $prompt) {
            $iteratedPrompts[] = $prompt;
        }

        $this->assertSame([$prompt1, $prompt2, $prompt3], $iteratedPrompts);
    }

    public function testToArray(): void
    {
        $prompt1 = new Prompt('one');
        $prompt2 = new Prompt('two');
        $prompt3 = new Prompt('three');

        $prompts = new Prompts($prompt1, $prompt2, $prompt3);

        $this->assertSame([$prompt1, $prompt2, $prompt3], $prompts->toArray());
    }
}
