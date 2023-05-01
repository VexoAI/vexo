<?php

declare(strict_types=1);

namespace Vexo\Embedding;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Embedding::class)]
final class EmbeddingTest extends TestCase
{
    public function testToArray(): void
    {
        $embedding = new Embedding([0.01, -0.03, 0.04, -0.01]);

        $this->assertEquals([0.01, -0.03, 0.04, -0.01], $embedding->toArray());
    }
}
