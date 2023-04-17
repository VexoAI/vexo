<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command\BrowseWebpage;

use PHPUnit\Framework\TestCase;

class TextSplitterTest extends TestCase
{
    public function testDivideTextIntoChunksWithDefaultMaxChunkSize(): void
    {
        $textSplitter = new TextSplitter();
        $text = str_repeat("This is a test paragraph.\n", 500);
        $chunks = $textSplitter->divideTextIntoChunks($text);

        // Test if chunks are divided correctly
        $this->assertCount(2, $chunks);
        $this->assertLessThanOrEqual(8192, strlen($chunks[0]));
        $this->assertLessThanOrEqual(8192, strlen($chunks[1]));
    }

    public function testDivideTextIntoChunksWithCustomMaxChunkSize(): void
    {
        $maxChunkSize = 200;
        $textSplitter = new TextSplitter($maxChunkSize);
        $text = str_repeat("This is a test paragraph.\n", 15);
        $chunks = $textSplitter->divideTextIntoChunks($text);

        // Test if chunks are divided correctly
        $this->assertCount(3, $chunks);
        $this->assertLessThanOrEqual($maxChunkSize, strlen($chunks[0]));
        $this->assertLessThanOrEqual($maxChunkSize, strlen($chunks[1]));
        $this->assertLessThanOrEqual($maxChunkSize, strlen($chunks[2]));
    }

    public function testDivideTextIntoChunksWithEmptyText(): void
    {
        $textSplitter = new TextSplitter();
        $text = "";
        $chunks = $textSplitter->divideTextIntoChunks($text);

        // Test if chunks are empty
        $this->assertCount(0, $chunks);
    }

    public function testChunksAreNotSplitMidParagraph(): void
    {
        $textSplitter = new TextSplitter(150);
        $text = str_repeat("START of a sentence with an END\n", 15);
        $chunks = $textSplitter->divideTextIntoChunks($text);

        // Test if chunks are not split mid-paragraph
        foreach ($chunks as $chunk) {
            $this->assertStringStartsWith('START', $chunk);
            $this->assertStringEndsWith('END', $chunk);
        }
    }
}
