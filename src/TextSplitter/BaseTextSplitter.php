<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;

abstract class BaseTextSplitter implements EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    /**
     * @var callable
     */
    protected $sizeFunction;

    public function __construct(
        protected int $chunkSize = 4000,
        protected int $minChunkOverlap = 200,
        ?callable $sizeFunction = null
    ) {
        if ($minChunkOverlap > $chunkSize) {
            throw new \InvalidArgumentException('Minimum chunk overlap cannot be greater than chunk size');
        }

        $this->sizeFunction = $sizeFunction ?? fn (string $text) => mb_strlen($text);
    }

    /**
     * @return string[]
     */
    abstract public function split(string $text): array;

    protected function mergeSplitsIntoChunks(array $splits, string $separator): array
    {
        $separatorSize = $this->size($separator);

        $chunks = [];
        $currentChunkSplits = [];
        $currentChunkSize = 0;

        foreach ($splits as $split) {
            $split = trim($split);
            $splitSize = $this->size($split);

            // Check if this split is empty, and skip if it is
            if ($splitSize === 0) {
                continue;
            }

            // Check if the length of the current chunk combined with this split is under the chunk size. If so, we can
            // add the split to the current chunk and move on to the next split.
            $currentChunkWithSplitSize = $currentChunkSize + $splitSize + (\count($currentChunkSplits) > 0 ? $separatorSize : 0);
            if ($currentChunkWithSplitSize <= $this->chunkSize) {
                $currentChunkSize = $currentChunkWithSplitSize;
                $currentChunkSplits[] = $split;
                continue;
            }

            // If we end up here but the current chunk is empty, it means that the current split is larger than the chunk
            // size. We will add it to the current chunk anyway. We will raise an event in the next iteration.
            if (empty($currentChunkSplits)) {
                $currentChunkSize += $splitSize;
                $currentChunkSplits[] = $split;
                continue;
            }

            // Check if the current chunk exceeds the chunk size. If so, it means that the previous split which was just
            // added made the chunk exceed the chunk size. This can happen in two scenarios:
            //
            // 1. We were given a split which is larger than the chunk size.
            // 2. We started a new chunk with a minimal overlap, and adding the first split after it made it exceed the
            //    chunk size.
            //
            // These cases are usually indicative of too high a minimum overlap, too low a chunk size, or an issue with
            // the text splitter resulting in too large splits.
            //
            // If the size is exceeded we will continue, but raise an event in case this needs to be handled.
            //
            if ($currentChunkSize > $this->chunkSize) {
                $this->eventDispatcher()->dispatch(
                    (new ChunkSizeExceeded($this->chunkSize, $this->minChunkOverlap, $currentChunkSplits))->for($this)
                );
            }

            // Adding the current split to our current chunk would exceed the chunk size, so this chunk is complete. We
            // will add it to our list of chunks and start a new chunk.
            $chunks[] = implode($separator, $currentChunkSplits);

            // We will now start a new chunk. We want to ensure that our new chunk will overlap with the end of the
            // previous chunk, so we will select splits from the end of the last chunk to be added to the beginning of
            // our new chunk.
            $newChunkSplits = [];
            $newChunkSize = 0;
            while ($newChunkSize < $this->minChunkOverlap && \count($currentChunkSplits) > 0) {
                $overlapSplit = array_pop($currentChunkSplits);
                $overlapSplitSize = $this->size($overlapSplit);

                $newChunkSize += $overlapSplitSize + (\count($newChunkSplits) > 0 ? $separatorSize : 0);
                array_unshift($newChunkSplits, $overlapSplit);
            }

            // Now that we have created the basis for our new chunk, we will set it as the current chunk and add the
            // current split to it. This may exceed the chunk size, which we will handle in the next iteration.
            $currentChunkSize = $newChunkSize + $splitSize + $separatorSize;
            $currentChunkSplits = array_merge($newChunkSplits, [$split]);
        }

        // Finally add the last chunk to our list of chunks
        $chunks[] = implode($separator, $currentChunkSplits);

        // If the last chunk exceeds the chunk size, we will raise an event in case this needs to be handled.
        if ($currentChunkSize > $this->chunkSize) {
            $this->eventDispatcher()->dispatch(
                (new ChunkSizeExceeded($this->chunkSize, $this->minChunkOverlap, $currentChunkSplits))->for($this)
            );
        }

        return $chunks;
    }

    protected function size(string $text): int
    {
        return ($this->sizeFunction)($text);
    }
}
