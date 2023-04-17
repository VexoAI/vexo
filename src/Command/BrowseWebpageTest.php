<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

final class BrowseWebpageTest extends TestCase
{
    public function testFromArrayReturnsValidBrowseWebpageInstance(): void
    {
        $arguments = [
            'url' => 'https://www.example.com',
            'question' => 'What is the title of the page?',
        ];

        $browseWebpage = BrowseWebpage::fromArray($arguments);

        $this->assertInstanceOf(BrowseWebpage::class, $browseWebpage);
        $this->assertSame($arguments, $browseWebpage->arguments());
    }

    public static function invalidDataProvider(): array
    {
        return [
            'missing_url' => [['question' => 'What is the title of the page?']],
            'invalid_url' => [['url' => 'invalid-url', 'question' => 'What is the title of the page?']],
            'missing_question' => [['url' => 'https://www.example.com']],
            'empty_question' => [['url' => 'https://www.example.com', 'question' => '']],
        ];
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFromArrayThrowsExceptionForInvalidArguments(array $arguments): void
    {
        $this->expectException(\InvalidArgumentException::class);

        BrowseWebpage::fromArray($arguments);
    }
}