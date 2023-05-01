<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use Vexo\Event\BaseEvent;

final class ChunkSizeExceeded extends BaseEvent
{
    public function __construct(
        public int $chunkSize,
        public int $minChunkOverlap,
        public array $currentChunkSplits
    ) {
    }
}
