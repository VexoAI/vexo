<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Vexo\Event\SomethingHappened;

final class ChunkSizeExceeded extends SomethingHappened
{
    public function __construct(
        public int $chunkSize,
        public int $minChunkOverlap,
        public array $currentChunkSplits
    ) {
    }
}
