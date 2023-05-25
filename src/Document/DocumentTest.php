<?php

declare(strict_types=1);

namespace Vexo\Document;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata;

#[CoversClass(Document::class)]
final class DocumentTest extends TestCase
{
    private Document $document;

    protected function setUp(): void
    {
        $this->document = new Document('some contents', new Metadata(['foo' => 'bar']));
    }

    public function testContents(): void
    {
        $this->assertSame('some contents', $this->document->contents());
    }

    public function testMetadata(): void
    {
        $this->assertSame('bar', $this->document->metadata()->get('foo'));
    }
}
