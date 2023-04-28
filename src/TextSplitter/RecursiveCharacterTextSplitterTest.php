<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RecursiveCharacterTextSplitter::class)]
final class RecursiveCharacterTextSplitterTest extends TestCase
{
    public function testSplit(): void
    {
        $textSplitter = new RecursiveCharacterTextSplitter(
            chunkSize: 40,
            minChunkOverlap: 0,
            separators: ["\n\n", "\n", ' ']
        );

        $text = <<<TEXT
            This paragraph will fit in a chunk.

            This is a paragraph which is too long
            for a single chunk, but each line will
            fit into a single chunk.

            This paragraph and this line which it contains will not fit, so it will be split up.

            This_is_an_extremely_long_word_that_will_be_split_to_fit_into_a_chunk.
            TEXT;

        $chunks = $textSplitter->split($text);

        $this->assertEquals(
            [
                'This paragraph will fit in a chunk.',
                'This is a paragraph which is too long',
                'for a single chunk, but each line will',
                'fit into a single chunk.',
                'This paragraph and this line which it',
                'contains will not fit, so it will be',
                'split up.',
                'This_is_an_extremely_long_word_that_will',
                '_be_split_to_fit_into_a_chunk.'
            ],
            $chunks
        );
    }
}
