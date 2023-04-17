<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class ReadFromFileTest extends TestCase
{
    public function testFromArrayReturnsValidReadFromFileInstance(): void
    {
        $arguments = [
            'file' => 'example.txt',
        ];

        $readFromFile = ReadFromFile::fromArray($arguments);

        $this->assertInstanceOf(ReadFromFile::class, $readFromFile);
        $this->assertSame($arguments, $readFromFile->arguments());
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing_file' => [[]],
            'empty_file' => [['file' => '']],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayThrowsExceptionForInvalidArguments(array $arguments): void
    {
        $this->expectException(\InvalidArgumentException::class);

        ReadFromFile::fromArray($arguments);
    }
}
