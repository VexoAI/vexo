<?php

declare(strict_types=1);

namespace Vexo\Document;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Document::class)]
final class DocumentTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $contents = 'My document contents';
        $metadata = new Metadata(['foo' => 'bar']);

        $document = new Document($contents, $metadata);

        $this->assertSame($contents, $document->contents());
        $this->assertSame($metadata, $document->metadata());
    }
}
