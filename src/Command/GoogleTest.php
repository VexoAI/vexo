<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class GoogleTest extends TestCase
{
    public function testFromArrayReturnsValidGoogleInstance(): void
    {
        $arguments = [
            'query' => 'What is the tallest building in the world?',
        ];

        $google = Google::fromArray($arguments);

        $this->assertInstanceOf(Google::class, $google);
        $this->assertSame($arguments, $google->arguments());
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing_query' => [[]],
            'empty_query' => [['query' => '']],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayThrowsExceptionForInvalidArguments(array $arguments): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Google::fromArray($arguments);
    }
}
