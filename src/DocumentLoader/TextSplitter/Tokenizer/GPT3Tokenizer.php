<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader\TextSplitter\Tokenizer;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer as Gioni06Gpt3Tokenizer;

final class GPT3Tokenizer implements Tokenizer
{
    public function __construct(
        private readonly Gioni06Gpt3Tokenizer $tokenizer
    ) {
    }

    public function encode(string $text): array
    {
        return $this->tokenizer->encode($text);
    }

    public function decode(array $tokens): string
    {
        return $this->tokenizer->decode($tokens);
    }
}
