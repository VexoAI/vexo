<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands\BrowseWebpage;

final class TextSplitter
{
    public function __construct(private int $maxChunkSize = 8192)
    {
    }

    public function divideTextIntoChunks(string $text): array
    {
        $chunks = [];
        $currentChunk = '';
        $paragraphs = $this->splitTextIntoParagraphs($text);

        foreach ($paragraphs as $paragraph) {
            $tempChunk = $currentChunk . "\n" . $paragraph;

            if (strlen($tempChunk) <= $this->maxChunkSize) {
                $currentChunk = $tempChunk;
            } else {
                $chunks[] = trim($currentChunk);
                $currentChunk = $paragraph;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return $chunks;
    }

    private function splitTextIntoParagraphs(string $text): array
    {
        return preg_split('/\n/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
