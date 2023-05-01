<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

final class RecursiveCharacterTextSplitter extends BaseTextSplitter
{
    public function __construct(
        int $chunkSize = 4000,
        int $minChunkOverlap = 200,
        ?callable $sizeFunction = null,
        private readonly array $separators = ["\n\n", "\n", ' ', '']
    ) {
        parent::__construct($chunkSize, $minChunkOverlap, $sizeFunction);
    }

    public function split(string $text): array
    {
        $finalChunks = [];

        // Find a separator that exists in the text
        $separator = $this->findFirstExistingSeparator($text);

        // Split the text into chunks using the separator
        $splits = ($separator === '')
            ? str_split($text)
            : explode($separator, $text);

        // Merge the splits into chunks, recursively splitting splits that are too large
        $goodSplits = [];
        foreach ($splits as $split) {
            // If the split is below the chunk size, we don't need to recursively split it
            if ($this->size($split) < $this->chunkSize) {
                $goodSplits[] = $split;
                continue;
            }

            // Before further splitting our split, merge the good splits into chunks
            $finalChunks = $this->mergeIntoFinalChunks($finalChunks, $goodSplits, $separator);
            $goodSplits = [];

            // Recursively split the remaining split into smaller chunks
            $recursiveChunks = $this->split($split);
            $finalChunks = array_merge($finalChunks, $recursiveChunks);
        }

        return $this->mergeIntoFinalChunks($finalChunks, $goodSplits, $separator);
    }

    private function findFirstExistingSeparator(string $text): string
    {
        foreach ($this->separators as $separator) {
            if (str_contains($text, (string) $separator)) {
                return $separator;
            }
        }

        return '';
    }

    private function mergeIntoFinalChunks(array $finalChunks, array $goodSplits, string $separator): array
    {
        if ($goodSplits === []) {
            return $finalChunks;
        }

        $mergedChunks = $this->mergeSplitsIntoChunks($goodSplits, $separator);

        return array_merge($finalChunks, $mergedChunks);
    }
}
