<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextFileLoader::class)]
final class TextFileLoaderTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $this->filesystem->write('MyTestFile.txt', 'My contents');
    }

    public function testLoad(): void
    {
        $loader = new TextFileLoader($this->filesystem, 'MyTestFile.txt');
        $documents = $loader->load();

        $this->assertCount(1, $documents);
        $this->assertSame('My contents', $documents->first()->contents());
        $this->assertSame('MyTestFile.txt', $documents->first()->metadata()->get('path'));
    }

    public function testLoadThrowsExceptionWhenFileDoesNotExist(): void
    {
        $this->expectException(FailedToLoadDocument::class);
        $this->expectExceptionMessage('Unable to read file');

        $loader = new TextFileLoader($this->filesystem, 'SomeNonExistantFile.txt');
        $loader->load();
    }
}
