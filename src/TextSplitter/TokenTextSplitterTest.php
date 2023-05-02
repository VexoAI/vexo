<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(TokenTextSplitter::class)]
final class TokenTextSplitterTest extends TestCase
{
    #[DataProvider('provideTextsAndExpectedChunks')]
    public function testSplit(string $text, int $chunkSize, int $minChunkOverlap, array $expectedChunks): void
    {
        $textSplitter = new TokenTextSplitter(
            tokenizer: new Gpt3Tokenizer(new Gpt3TokenizerConfig()),
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap
        );

        $chunks = $textSplitter->split($text);

        $this->assertEquals($expectedChunks, $chunks);
    }

    public static function provideTextsAndExpectedChunks(): array
    {
        return [
            'basic text' => [
                'text' => <<<TEXT
                    Many words map to one token, but some don't: indivisible.

                    Unicode characters like emojis may be split into many tokens containing the underlying bytes: 👋

                    Sequences of characters commonly found next to each other may be grouped together: 1234567890
                    TEXT,
                'chunkSize' => 20,
                'minChunkOverlap' => 0,
                'expectedChunks' => [
                    "Many words map to one token, but some don't: indivisible.\n\nUnic",
                    "ode characters like emojis may be split into many tokens containing the underlying bytes: 👋\n",
                    "\nSequences of characters commonly found next to each other may be grouped together: 1234567890",
                ]
            ],
            'basic text with overlap' => [
                'text' => <<<TEXT
                    Many words map to one token, but some don't: indivisible.

                    Unicode characters like emojis may be split into many tokens containing the underlying bytes: 👋

                    Sequences of characters commonly found next to each other may be grouped together: 1234567890
                    TEXT,
                'chunkSize' => 20,
                'minChunkOverlap' => 5,
                'expectedChunks' => [
                    "Many words map to one token, but some don't: indivisible.\n\nUnic",
                    ".\n\nUnicode characters like emojis may be split into many tokens containing the underlying",
                    " many tokens containing the underlying bytes: 👋\n\nSequences of characters commonly found next to each",
                    ' commonly found next to each other may be grouped together: 1234567890'
                ]
            ]
        ];
    }
}
