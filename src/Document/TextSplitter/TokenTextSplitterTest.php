<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vexo\Document\TextSplitter\Tokenizer\FakeTokenizer;

#[CoversClass(TokenTextSplitter::class)]
final class TokenTextSplitterTest extends TestCase
{
    public function testConstructorThrowsExceptionWhenMinChunkOverlapIsGreaterThanChunkSize(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minimum chunk overlap cannot be greater than chunk size');

        new TokenTextSplitter(new FakeTokenizer(), 100, 200);
    }

    #[DataProvider('provideTestSplitData')]
    public function testSplit(
        string $text,
        int $chunkSize,
        int $minChunkOverlap,
        array $expectedChunks,
        array $fakeTextToTokensMap
    ): void {
        $textSplitter = new TokenTextSplitter(
            tokenizer: new FakeTokenizer($fakeTextToTokensMap),
            chunkSize: $chunkSize,
            minChunkOverlap: $minChunkOverlap
        );

        $this->assertEquals($expectedChunks, $textSplitter->split($text));
    }

    public static function provideTestSplitData(): array
    {
        return [
            'basic text' => [
                'text' => 'Roses are red, violets are blue.',
                'chunkSize' => 4,
                'minChunkOverlap' => 0,
                'expectedChunks' => [
                    'Roses are red',
                    ', violets',
                    ' are blue.'
                ],
                [
                    'Roses are red, violets are blue.' => [49, 4629, 389, 2266, 11, 410, 952, 5289, 389, 4171, 13],
                    'Roses are red' => [49, 4629, 389, 2266],
                    ', violets' => [11, 410, 952, 5289],
                    ' are blue.' => [389, 4171, 13]
                ]
            ],
            'basic text with overlap' => [
                'text' => 'Roses are red, violets are blue.',
                'chunkSize' => 5,
                'minChunkOverlap' => 2,
                'expectedChunks' => [
                    'Roses are red,',
                    ' red, violets',
                    'iolets are blue.'
                ],
                [
                    'Roses are red, violets are blue.' => [49, 4629, 389, 2266, 11, 410, 952, 5289, 389, 4171, 13],
                    'Roses are red,' => [49, 4629, 389, 2266, 11],
                    ' red, violets' => [2266, 11, 410, 952, 5289],
                    'iolets are blue.' => [952, 5289, 389, 4171, 13]
                ]
            ]
        ];
    }
}
