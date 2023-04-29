<?php

declare(strict_types=1);

namespace Vexo\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Completion::class)]
final class CompletionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $text = 'text';
        $completion = new Completion($text);

        $this->assertSame($text, $completion->text());
    }

    public function testToString(): void
    {
        $text = 'text';
        $completion = new Completion($text);

        $this->assertSame($text, (string) $completion);
    }
}
