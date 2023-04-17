<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;

final class ReadFromFileHandlerTest extends TestCase
{
    private Filesystem $filesystem;

    private ReadFromFileHandler $handler;

    protected function setUp(): void
    {
        $adapter = new InMemoryFilesystemAdapter();
        $this->filesystem = new Filesystem($adapter);
        $this->handler = new ReadFromFileHandler($this->filesystem);
    }

    public function testHandlesReturnsCorrectCommandClasses(): void
    {
        $this->assertSame([ReadFromFile::class], $this->handler->handles());
    }

    public function testHandleReadsFromFileAndReturnsCommandResult(): void
    {
        $filename = 'test.txt';
        $fileContent = 'Sample content';
        $this->filesystem->write($filename, $fileContent);

        $command = new ReadFromFile($filename);

        $result = $this->handler->handle($command);

        $this->assertSame(['contents' => $fileContent], $result->data);
    }

    public function testHandleThrowsExceptionForInvalidCommand(): void
    {
        $invalidCommand = $this->createMock(Command::class);

        $this->expectException(\InvalidArgumentException::class);

        $this->handler->handle($invalidCommand);
    }
}
