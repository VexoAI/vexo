<?php

declare(strict_types=1);

namespace Vexo\Document;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Documents::class)]
final class DocumentsTest extends TestCase
{
    public function testGetType(): void
    {
        $documents = new Documents();

        $this->assertSame(Document::class, $documents->getType());
    }
}
