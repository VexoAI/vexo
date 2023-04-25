<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TextExtractor::class)]
final class TextExtractorTest extends TestCase
{
    private TextExtractor $textExtractor;

    protected function setUp(): void
    {
        $this->textExtractor = new TextExtractor();
    }

    public function testExtractEmptyString(): void
    {
        $this->assertSame('', $this->textExtractor->extract(''));
    }

    public function testExtractNoUsefulContent(): void
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Empty</title>
            </head>
            <body>
            </body>
            </html>
            HTML;

        $this->assertSame('', $this->textExtractor->extract($html));
    }

    public function testExtractWithUsefulContent(): void
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Sample Page</title>
            </head>
            <body>
                <h1>Welcome to the sample page!</h1>
                <p>This is a sample paragraph.</p>
            </body>
            </html>
            HTML;

        $expected = "Welcome to the sample page!\nThis is a sample paragraph.";
        $this->assertSame($expected, $this->textExtractor->extract($html));
    }

    public function testExtractWithTagsToRemove(): void
    {
        $html = <<<HTML
            <!DOCTYPE html>
            <html>
            <head>
                <title>Page with Tags to Remove</title>
                <style>body { background-color: white; }</style>
                <script>console.log('Hello, world!');</script>
            </head>
            <body>
                <h1>Welcome to the page with tags to remove!</h1>
                <noscript>This site requires JavaScript.</noscript>
                <p>This is a sample paragraph   with   some    space.</p>
            </body>
            </html>
            HTML;

        $expected = "Welcome to the page with tags to remove!\nThis is a sample paragraph with some space.";
        $this->assertSame($expected, $this->textExtractor->extract($html));
    }
}
