<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Vexo\Tokenizer\Tokenizer;

final class TokenTextSplitter implements TextSplitter
{
    public function __construct(
        private readonly Tokenizer $tokenizer,
        private readonly int $chunkSize = 4000,
        private readonly int $minChunkOverlap = 200
    ) {
        if ($minChunkOverlap > $chunkSize) {
            throw new \InvalidArgumentException('Minimum chunk overlap cannot be greater than chunk size');
        }
    }

    public function split(string $text): array
    {
        $tokens = $this->tokenizer->encode($text);

        $chunks = [];
        $tokenCount = \count($tokens);
        for ($i = 0; $i < $tokenCount; $i += $this->chunkSize - $this->minChunkOverlap) {
            $chunks[] = $this->tokenizer->decode(
                \array_slice($tokens, $i, $this->chunkSize)
            );

            if (($i + $this->chunkSize) >= $tokenCount) {
                break;
            }
        }

        return $chunks;
    }
}
