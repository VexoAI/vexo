<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain\Retriever;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;

#[CoversClass(CallbackRetriever::class)]
final class CallbackRetrieverTest extends TestCase
{
    public function testRetrieve(): void
    {
        $retriever = new CallbackRetriever(
            retrieverFunction: fn (string $query): DocumentsContract => new Documents(
                [
                    new Document('My amazing content', new Metadata(['id' => 1])),
                    new Document('My amazing content', new Metadata(['id' => 2])),
                ]
            )
        );

        $results = $retriever->retrieve('Something amazing');

        $this->assertCount(2, $results);

        $this->assertEquals(1, $results[0]->metadata()->get('id'));
        $this->assertEquals(2, $results[1]->metadata()->get('id'));
    }
}
