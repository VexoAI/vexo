<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;

/**
 * @todo BaseTextSplitter now has a bunch of logic that we don't need here. We should refactor it to be more generic
 * @todo We need a TextSplitter interface
 * @todo We need to intoduce a Tokenizer interface
 */
final class TokenTextSplitter extends BaseTextSplitter
{
    public function __construct(
        private readonly Gpt3Tokenizer $tokenizer,
        int $chunkSize = 4000,
        int $minChunkOverlap = 200
    ) {
        parent::__construct(
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap
        );
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
