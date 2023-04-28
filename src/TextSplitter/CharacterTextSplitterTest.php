<?php

declare(strict_types=1);

namespace Vexo\TextSplitter;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\SomethingHappened;

#[CoversClass(CharacterTextSplitter::class)]
final class CharacterTextSplitterTest extends TestCase
{
    public function testSplit(): void
    {
        $textSplitter = new CharacterTextSplitter(
            chunkSize: 5,
            minChunkOverlap: 0,
            separator: ' '
        );

        $chunks = $textSplitter->split('Foo bar baz');

        $this->assertEquals(['Foo', 'bar', 'baz'], $chunks);
    }

    public function testSplitWithoutSeparatorReturnsTextAsSingleChunk(): void
    {
        $textSplitter = new CharacterTextSplitter(separator: '');

        $chunks = $textSplitter->split('Foo bar baz');

        $this->assertEquals(['Foo bar baz'], $chunks);
    }

    public function testSplitWithoutSeparatorAndTooLargeTextRaisesEvent(): void
    {
        $dispatchedEvents = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            SomethingHappened::class,
            function (SomethingHappened $event) use (&$dispatchedEvents): void {
                $dispatchedEvents[] = $event;
            }
        );

        $textSplitter = new CharacterTextSplitter(chunkSize: 3, minChunkOverlap: 0, separator: '');
        $textSplitter->useEventDispatcher($eventDispatcher);

        $textSplitter->split('Foo bar baz');

        $this->assertCount(1, $dispatchedEvents);
        $this->assertInstanceOf(ChunkSizeExceeded::class, $dispatchedEvents[0]);
    }
}
