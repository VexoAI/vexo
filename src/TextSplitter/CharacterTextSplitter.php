<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

final class CharacterTextSplitter extends BaseTextSplitter
{
    public function __construct(
        int $chunkSize = 4000,
        int $minChunkOverlap = 200,
        bool $trimWhitespace = false,
        ?callable $sizeFunction = null,
        private readonly string $separator = "\n\n"
    ) {
        parent::__construct(
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap,
            trimWhitespace: $trimWhitespace,
            sizeFunction: $sizeFunction
        );
    }

    public function split(string $text): array
    {
        if ($this->separator === '') {
            $splits = [$text];

            if ($this->size($text) > $this->chunkSize) {
                $this->emit(new ChunkSizeExceeded($this->chunkSize, $this->minChunkOverlap, $splits));
            }

            return $splits;
        }

        $splits = explode($this->separator, $text);

        return $this->mergeSplitsIntoChunks($splits, $this->separator);
    }
}
