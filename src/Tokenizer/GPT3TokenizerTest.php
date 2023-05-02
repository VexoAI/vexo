<?php

declare(strict_types=1);

namespace Vexo\Tokenizer;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer as Gioni06Gpt3Tokenizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GPT3Tokenizer::class)]
final class GPT3TokenizerTest extends TestCase
{
    private Gioni06Gpt3Tokenizer $gioni06Gpt3Tokenizer;

    private GPT3Tokenizer $tokenizer;

    protected function setUp(): void
    {
        $this->gioni06Gpt3Tokenizer = new class() extends Gioni06Gpt3Tokenizer {
            public function __construct()
            {
            }

            public function encode(string $text): array
            {
                return [15496, 612, 0];
            }

            public function decode(array $tokens): string
            {
                return 'Hello there!';
            }
        };

        $this->tokenizer = new GPT3Tokenizer($this->gioni06Gpt3Tokenizer);
    }

    public function testEncode(): void
    {
        $this->assertSame([15496, 612, 0], $this->tokenizer->encode('Hello there!'));
    }

    public function testDecode(): void
    {
        $this->assertSame('Hello there!', $this->tokenizer->decode([15496, 612, 0]));
    }
}
