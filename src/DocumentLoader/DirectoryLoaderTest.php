<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemReader;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\StorageAttributes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;

#[CoversClass(DirectoryLoader::class)]
final class DirectoryLoaderTest extends TestCase
{
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = new Filesystem(new InMemoryFilesystemAdapter());
        $this->filesystem->write('one/foo.txt', 'My contents');
        $this->filesystem->write('one/two/bar.txt', 'My contents');
        $this->filesystem->write('baz.txt', 'My contents');
        $this->filesystem->write('fudge.txt', 'My contents');
    }

    public function testLoad(): void
    {
        $loader = new DirectoryLoader($this->filesystem, '/');
        $documents = $loader->load();

        $this->assertCount(2, $documents);
        $this->assertSame('baz.txt', $documents->first()->metadata()->get('path'));
        $this->assertSame('fudge.txt', $documents->last()->metadata()->get('path'));
    }

    public function testLoadRecursive(): void
    {
        $loader = new DirectoryLoader($this->filesystem, '/', loadRecursive: true);
        $documents = $loader->load();

        $this->assertCount(4, $documents);
    }

    public function testLoadWithFilter(): void
    {
        $loader = new DirectoryLoader(
            filesystem: $this->filesystem,
            path: '/',
            filter: fn (StorageAttributes $attributes): bool => $attributes->path() === 'baz.txt'
        );
        $documents = $loader->load();

        $this->assertCount(1, $documents);
        $this->assertSame('baz.txt', $documents->first()->metadata()->get('path'));
    }

    public function testLoadWithCustomFileLoader(): void
    {
        $loader = new DirectoryLoader(
            filesystem: $this->filesystem,
            path: '/',
            fileLoader: fn (FilesystemReader $filesystem, string $path): DocumentsContract => new Documents([new Document('Custom loader')])
        );
        $documents = $loader->load();

        $this->assertCount(2, $documents);
        $this->assertSame('Custom loader', $documents->first()->contents());
    }

    public function testLoadThrowsExceptionWhenDirectoryDoesNotExist(): void
    {
        $this->expectException(FailedToLoadDocument::class);
        $this->expectExceptionMessage('No documents found in directory');

        $loader = new DirectoryLoader($this->filesystem, 'two');
        $loader->load();
    }

    public function testLoadThrowsExceptionWhenFileLoadFails(): void
    {
        $this->expectException(FailedToLoadDocument::class);
        $this->expectExceptionMessage('Some error');

        $loader = new DirectoryLoader(
            filesystem: $this->filesystem,
            path: '/',
            filter: fn (StorageAttributes $attributes) => throw new \Exception('Some error'),
        );
        $loader->load();
    }
}
