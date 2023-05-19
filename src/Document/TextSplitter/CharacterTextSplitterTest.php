<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(CharacterTextSplitter::class)]
final class CharacterTextSplitterTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenMinChunkOverlapIsGreaterThanChunkSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum chunk overlap cannot be greater than chunk size');

        new CharacterTextSplitter(100, 200);
    }

    public function testSplitThrowsExceptionWhenChunkSizeIsExceeded(): void
    {
        $textSplitter = new CharacterTextSplitter(
            chunkSize: 40,
            minChunkOverlap: 10,
            separators: [' '],
        );

        $this->expectException(ChunkSizeExceeded::class);
        $textSplitter->split('Lorem ipsum_dolor_sit_amet,_consectetur adipiscing_elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');
    }

    #[DataProvider('provideTextsAndChunks')]
    public function testMergeSplits(
        int $chunkSize,
        int $minChunkOverlap,
        array $separators,
        string $text,
        array $expectedChunks,
        bool $trimWhitespace = false
    ): void {
        $textSplitter = new CharacterTextSplitter(
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap,
            separators: $separators,
            trimWhitespace: $trimWhitespace
        );

        $chunks = $textSplitter->split($text);

        $this->assertEquals($expectedChunks, $chunks);
    }

    public static function provideTextsAndChunks(): array
    {
        return [
            'single chunk' => [
                4000,
                200,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'two chunks' => [
                120,
                20,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna',
                    'labore et dolore magna aliqua.',
                ]
            ],
            'single chunks with too large a split size' => [
                120,
                20,
                ["\n"],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliq',
                    'et dolore magna aliqua.'
                ]
            ],
            'with superfluous spacing' => [
                50,
                10,
                [' '],
                'Lorem ipsum dolor sit amet,    consectetur adipiscing elit, sed do eiusmod tempor incididunt ut   labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing',
                    'adipiscing elit, sed do eiusmod tempor incididunt',
                    'incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'with empty splits' => [
                50,
                10,
                ['_'],
                'Lorem_ipsum_dolor_sit_amet,_consectetur_ _ ___adipiscing_elit,_sed_do_eiusmod__ _ __ _tempor_incididunt_ut_labore_et_dolore_magna_aliqua.',
                [
                    'Lorem_ipsum_dolor_sit_amet,_consectetur_ _ ',
                    'consectetur_ _ _adipiscing_elit,_sed_do_eiusmod_ ',
                    'do_eiusmod_ _ _ _tempor_incididunt_ut_labore_et',
                    'ut_labore_et_dolore_magna_aliqua.'
                ]
            ],
            'with empty splits and trim whitespace' => [
                50,
                10,
                ['_'],
                'Lorem_ipsum_dolor_sit_amet,_consectetur_ _ ___adipiscing_elit,_sed_do_eiusmod__ _ __ _tempor_incididunt_ut_labore_et_dolore_magna_aliqua.',
                [
                    'Lorem_ipsum_dolor_sit_amet,_consectetur_adipiscing',
                    'adipiscing_elit,_sed_do_eiusmod_tempor_incididunt',
                    'incididunt_ut_labore_et_dolore_magna_aliqua.'
                ],
                true
            ],
            'with splits and overlap causing last chunk to exceed chunk size' => [
                50,
                10,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor_incididunt_ut_labore_et_dolore_magna_aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing',
                    'adipiscing elit, sed do eiusmod',
                    'tempor_incididunt_ut_labore_et_dolore_magna_aliqua',
                    'gna_aliqua.'
                ]
            ],
            'tiny overlap' => [
                50,
                1,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing',
                    'adipiscing elit, sed do eiusmod tempor incididunt',
                    'incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'large overlap' => [
                120,
                105,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna',
                    'ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'without overlap' => [
                40,
                0,
                [' '],
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur',
                    'adipiscing elit, sed do eiusmod tempor',
                    'incididunt ut labore et dolore magna',
                    'aliqua.'
                ]
            ],
            'multiple separators' => [
                40,
                0,
                ["\n\n", "\n", ' '],
                <<<TEXT
                    This paragraph will fit in a chunk.

                    This is a paragraph which is too long
                    for a single chunk, but each line will
                    fit into a single chunk.

                    This paragraph and this line which it contains will not fit, so it will be split up.

                    This_is_an_extremely_long_word_that_will_be_split_to_fit_into_a_chunk.
                    TEXT,
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
                ]
            ]
        ];
    }
}
