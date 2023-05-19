<?php

declare(strict_types=1);

namespace Vexo\Document\TextSplitter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Metadata;
use Vexo\Document\Document;
use Vexo\Document\Documents;

#[CoversClass(SplitDocumentsBehavior::class)]
final class SplitDocumentsBehaviorTest extends TestCase
{
    public function testSplitDocuments(): void
    {
        $textSplitter = new class() implements TextSplitter {
            use SplitDocumentsBehavior;

            public function split(string $text): array
            {
                return explode("\n", $text);
            }
        };

        $splitDocuments = $textSplitter->splitDocuments(
            new Documents([
                new Document("Roses are red,\nViolets are blue.", new Metadata(['id' => 1])),
                new Document("Once upon a midnight dreary,\nWhile I pondered, weak and weary.", new Metadata(['id' => 2]))
            ]),
        );

        $this->assertCount(4, $splitDocuments);

        $this->assertEquals(1, $splitDocuments[0]->metadata()->get('id'));
        $this->assertEquals('Roses are red,', $splitDocuments[0]->contents());

        $this->assertEquals(1, $splitDocuments[1]->metadata()->get('id'));
        $this->assertEquals('Violets are blue.', $splitDocuments[1]->contents());

        $this->assertEquals(2, $splitDocuments[2]->metadata()->get('id'));
        $this->assertEquals('Once upon a midnight dreary,', $splitDocuments[2]->contents());

        $this->assertEquals(2, $splitDocuments[3]->metadata()->get('id'));
        $this->assertEquals('While I pondered, weak and weary.', $splitDocuments[3]->contents());
    }
}
