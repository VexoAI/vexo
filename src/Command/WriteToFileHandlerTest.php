<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

final class WriteToFileHandlerTest extends TestCase
{
    private Filesystem $filesystem;

    private WriteToFileHandler $handler;

    protected function setUp(): void
    {
        $adapter = new InMemoryFilesystemAdapter();
        $this->filesystem = new Filesystem($adapter);
        $this->handler = new WriteToFileHandler($this->filesystem);
    }

    public function testHandlesReturnsCorrectCommandClasses(): void
    {
        $this->assertSame([WriteToFile::class], $this->handler->handles());
    }

    public function testHandleWritesToFileAndReturnsCommandResult(): void
    {
        $command = new WriteToFile('test.txt', 'Sample content');

        $result = $this->handler->handle($command);

        $this->assertSame(['Contents written successfully'], $result->data);
        $this->assertTrue($this->filesystem->has($command->file));
        $this->assertSame($command->contents, $this->filesystem->read($command->file));
    }

    public function testHandleThrowsExceptionForInvalidCommand(): void
    {
        $invalidCommand = $this->createMock(Command::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->handler->handle($invalidCommand);
    }
}
