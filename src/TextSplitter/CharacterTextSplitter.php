<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

final class CharacterTextSplitter implements TextSplitter
{
    use SplitDocumentsBehavior;

    /**
     * @var callable
     */
    private $sizeFunction;

    public function __construct(
        private readonly int $chunkSize = 4000,
        private readonly int $minChunkOverlap = 200,
        private readonly bool $trimWhitespace = false,
        private readonly array $separators = ["\n\n", "\n", ' ', ''],
        ?callable $sizeFunction = null,
    ) {
        if ($minChunkOverlap > $chunkSize) {
            throw new \InvalidArgumentException('Minimum chunk overlap cannot be greater than chunk size');
        }

        $this->sizeFunction = $sizeFunction ?? fn (string $text): int => mb_strlen($text);
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

    private function size(string $text): int
    {
        return ($this->sizeFunction)($text);
    }

    private function mergeIntoFinalChunks(array $finalChunks, array $goodSplits, string $separator): array
    {
        if ($goodSplits === []) {
            return $finalChunks;
        }

        $mergedChunks = $this->mergeSplitsIntoChunks($goodSplits, $separator);

        return array_merge($finalChunks, $mergedChunks);
    }

    private function mergeSplitsIntoChunks(array $splits, string $separator = ''): array
    {
        $separatorSize = $this->size($separator);

        $chunks = [];
        $currentChunkSplits = [];
        $currentChunkSize = 0;

        foreach ($splits as $split) {
            $split = (string) $split;
            $split = $this->trimWhitespace ? trim($split) : $split;

            $splitSize = $this->size($split);

            // Check if this split is empty, and skip if it is
            if ($splitSize === 0) {
                continue;
            }

            // Check if the length of the current chunk combined with this split is under the chunk size. If so, we can
            // add the split to the current chunk and move on to the next split.
            $currentChunkWithSplitSize = $currentChunkSize + $splitSize + ($currentChunkSplits !== [] ? $separatorSize : 0);
            if ($currentChunkWithSplitSize <= $this->chunkSize) {
                $currentChunkSize = $currentChunkWithSplitSize;
                $currentChunkSplits[] = $split;
                continue;
            }

            // Check if the current chunk exceeds the chunk size. If so, it means that the previous split which was just
            // added made the chunk exceed the chunk size. This can happen when we started a new chunk with a minimal
            // overlap, and adding the first split after it made it exceed the chunk size.
            //
            // This is usually indicative of too high a minimum overlap or too low a chunk size.
            //
            if ($currentChunkSize > $this->chunkSize) {
                throw new SorryChunkSizeExceeded('Chunk size exceeded due to too high an overlap');
            }

            // Adding the current split to our current chunk would exceed the chunk size, so this chunk is complete. We
            // will add it to our list of chunks and start a new chunk.
            $chunks[] = implode($separator, $currentChunkSplits);

            // We will now start a new chunk. We want to ensure that our new chunk will overlap with the end of the
            // previous chunk, so we will select splits from the end of the last chunk to be added to the beginning of
            // our new chunk.
            $newChunkSplits = [];
            $newChunkSize = 0;
            while ($newChunkSize < $this->minChunkOverlap && $currentChunkSplits !== []) {
                $overlapSplit = array_pop($currentChunkSplits);
                $overlapSplitSize = $this->size($overlapSplit);

                $newChunkSize += $overlapSplitSize + ($newChunkSplits !== [] ? $separatorSize : 0);
                array_unshift($newChunkSplits, $overlapSplit);
            }

            // Now that we have created the basis for our new chunk, we will set it as the current chunk and add the
            // current split to it. This may exceed the chunk size, which we will handle in the next iteration.
            $currentChunkSize = $newChunkSize + $splitSize + $separatorSize;
            $currentChunkSplits = [...$newChunkSplits, $split];
        }

        // Finally add the last chunk to our list of chunks
        $chunks[] = implode($separator, $currentChunkSplits);

        return $chunks;
    }
}
