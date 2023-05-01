<?php

declare(strict_types=1);

namespace Vexo\Embedding;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Embeddings::class)]
final class EmbeddingsTest extends TestCase
{
    public function testGetType(): void
    {
        $embeddings = new Embeddings();

        $this->assertSame(Embedding::class, $embeddings->getType());
    }
}
