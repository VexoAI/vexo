<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CsvFileLoader::class)]
final class CsvFileLoaderTest extends TestCase
{
    private vfsStreamDirectory $filesystem;

    protected function setUp(): void
    {
        $this->filesystem = vfsStream::setup(
            rootDirName: 'root',
            structure: [
                'documents.csv' => <<<CSV
                    id,title,contents
                    1,"A poem","Roses are red, violets are blue."
                    CSV,
                'incorrect.csv' => ''
            ]
        );
    }

    public function testLoad(): void
    {
        $loader = new CsvFileLoader($this->filesystem->url() . '/documents.csv');

        $documents = $loader->load();

        $this->assertCount(1, $documents);
        $this->assertEquals('Roses are red, violets are blue.', $documents[0]->contents());
        $this->assertEquals('1', $documents[0]->metadata()->get('id'));
        $this->assertEquals(1, $documents[0]->metadata()->get('offset'));
        $this->assertEquals('A poem', $documents[0]->metadata()->get('title'));
        $this->assertEquals('vfs://root/documents.csv', $documents[0]->metadata()->get('path'));
    }

    public function testLoadThrowsExceptionWhenFileCannotBeRead(): void
    {
        $loader = new CsvFileLoader($this->filesystem->url() . '/incorrect.csv');

        $this->expectException(FailedToLoadDocument::class);
        $loader->load();
    }
}
