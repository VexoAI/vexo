<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader\TextSplitter\Tokenizer;

final class FakeTokenizer implements Tokenizer
{
    public function __construct(
        private readonly array $textToTokens = []
    ) {
    }

    public function encode(string $text): array
    {
        if (\array_key_exists($text, $this->textToTokens)) {
            return $this->textToTokens[$text];
        }

        throw new \InvalidArgumentException(sprintf('Text %s cannot be mapped to tokens', $text));
    }

    public function decode(array $tokens): string
    {
        $text = array_search($tokens, $this->textToTokens, true);
        if ($text !== false) {
            return $text;
        }

        throw new \InvalidArgumentException(sprintf('Tokens %s cannot be mapped to text', implode(', ', $tokens)));
    }
}
