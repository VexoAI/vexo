<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class WriteToFileTest extends TestCase
{
    public function testFromArrayReturnsValidWriteToFileInstance(): void
    {
        $arguments = [
            'file' => 'example.txt',
            'contents' => 'This is the example content.',
        ];

        $writeToFile = WriteToFile::fromArray($arguments);

        $this->assertInstanceOf(WriteToFile::class, $writeToFile);
        $this->assertSame($arguments, $writeToFile->arguments());
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing_file' => [['contents' => 'This is the example content.']],
            'empty_file' => [['file' => '', 'contents' => 'This is the example content.']],
            'missing_contents' => [['file' => 'example.txt']],
            'empty_contents' => [['file' => 'example.txt', 'contents' => '']],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayThrowsExceptionForInvalidArguments(array $arguments): void
    {
        $this->expectException(\InvalidArgumentException::class);

        WriteToFile::fromArray($arguments);
    }
}
