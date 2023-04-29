<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Event\SomethingHappened;

#[CoversClass(BaseTextSplitter::class)]
#[IgnoreClassForCodeCoverage(ChunkSizeExceeded::class)]
final class BaseTextSplitterTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenMinChunkOverlapIsGreaterThanChunkSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum chunk overlap cannot be greater than chunk size');

        new CharacterTextSplitter(100, 200);
    }

    #[DataProvider('provideTextsAndChunks')]
    public function testMergeSplits(
        int $chunkSize,
        int $minChunkOverlap,
        string $separator,
        string $text,
        array $expectedChunks,
        bool $shouldDispatchChunkSizeExceeded = false
    ): void {
        $dispatchedEvents = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            SomethingHappened::class,
            function (SomethingHappened $event) use (&$dispatchedEvents): void {
                $dispatchedEvents[] = $event;
            }
        );

        $textSplitter = new CharacterTextSplitter(
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap,
            separator: $separator
        );
        $textSplitter->useEventDispatcher($eventDispatcher);

        $chunks = $textSplitter->split($text);

        $this->assertEquals($expectedChunks, $chunks);

        if ($shouldDispatchChunkSizeExceeded) {
            $this->assertCount(1, $dispatchedEvents);
            $this->assertInstanceOf(ChunkSizeExceeded::class, $dispatchedEvents[0]);
        }

        if ( ! $shouldDispatchChunkSizeExceeded) {
            $this->assertCount(0, $dispatchedEvents);
        }
    }

    public static function provideTextsAndChunks(): array
    {
        return [
            'single chunk' => [
                4000,
                200,
                ' ',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'two chunks' => [
                120,
                20,
                ' ',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna',
                    'labore et dolore magna aliqua.',
                ]
            ],
            'single chunks with too large a split size' => [
                120,
                20,
                "\n",
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ],
                true
            ],
            'with superfluous spacing' => [
                50,
                10,
                ' ',
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
                '_',
                'Lorem_ipsum_dolor_sit_amet,_consectetur_ _ ___adipiscing_elit,_sed_do_eiusmod__ _ __ _tempor_incididunt_ut_labore_et_dolore_magna_aliqua.',
                [
                    'Lorem_ipsum_dolor_sit_amet,_consectetur_adipiscing',
                    'adipiscing_elit,_sed_do_eiusmod_tempor_incididunt',
                    'incididunt_ut_labore_et_dolore_magna_aliqua.'
                ]
            ],
            'with splits and overlap causing exceeding chunk size' => [
                40,
                10,
                ' ',
                'Lorem ipsum_dolor_sit_amet,_consectetur adipiscing_elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum_dolor_sit_amet,_consectetur',
                    'ipsum_dolor_sit_amet,_consectetur adipiscing_elit,', // Violates chunk size due to large split being used as overlap
                    'adipiscing_elit, sed do eiusmod tempor',
                    'eiusmod tempor incididunt ut labore et',
                    'ut labore et dolore magna aliqua.'
                ],
                true
            ],
            'with splits and overlap causing last chunk to exceed chunk size' => [
                50,
                10,
                ' ',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor_incididunt_ut_labore_et_dolore_magna_aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing',
                    'adipiscing elit, sed do eiusmod',
                    'do eiusmod tempor_incididunt_ut_labore_et_dolore_magna_aliqua.'
                ],
                true
            ],
            'tiny overlap' => [
                50,
                1,
                ' ',
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
                ' ',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna',
                    'ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                ]
            ],
            'without overlap' => [
                40,
                0,
                ' ',
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                [
                    'Lorem ipsum dolor sit amet, consectetur',
                    'adipiscing elit, sed do eiusmod tempor',
                    'incididunt ut labore et dolore magna',
                    'aliqua.'
                ]
            ]
        ];
    }
}
