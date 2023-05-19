<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter\Tokenizer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeTokenizer::class)]
final class FakeTokenizerTest extends TestCase
{
    private FakeTokenizer $fakeTokenizer;

    protected function setUp(): void
    {
        $this->fakeTokenizer = new FakeTokenizer(
            ['Hello there!' => [15496, 612, 0]]
        );
    }

    public function testEncodeMapsTextToTokens(): void
    {
        $this->assertSame([15496, 612, 0], $this->fakeTokenizer->encode('Hello there!'));
    }

    public function testEncodeThrowExceptionWithUnknownText(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fakeTokenizer->encode('This is not known!');
    }

    public function testDecodeMapsTokensToText(): void
    {
        $this->assertSame('Hello there!', $this->fakeTokenizer->decode([15496, 612, 0]));
    }

    public function testDecodeThrowExceptionWithUnknownTokens(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->fakeTokenizer->decode([1212, 318, 407, 1900, 0]);
    }
}
