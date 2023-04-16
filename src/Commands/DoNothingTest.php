<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use PHPUnit\Framework\TestCase;

final class DoNothingTest extends TestCase
{
    public function testFromArrayReturnsValidDoNothingInstance(): void
    {
        $arguments = [
            'reason' => 'No further action is required.',
        ];

        $doNothing = DoNothing::fromArray($arguments);

        $this->assertInstanceOf(DoNothing::class, $doNothing);
        $this->assertSame($arguments, $doNothing->arguments());
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

        DoNothing::fromArray($arguments);
    }
}
