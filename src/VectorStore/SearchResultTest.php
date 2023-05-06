<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Metadata\Implementation\Metadata;

#[CoversClass(SearchResult::class)]
final class SearchResultTest extends TestCase
{
    private SearchResult $searchResult;

    protected function setUp(): void
    {
        $this->searchResult = new SearchResult('10d0e3a4b28e', 0.5, new Metadata(['foo' => 'bar']));
    }

    public function testId(): void
    {
        $this->assertSame('10d0e3a4b28e', $this->searchResult->id());
    }

    public function testScore(): void
    {
        $this->assertSame(0.5, $this->searchResult->score());
    }

    public function testMetadata(): void
    {
        $this->assertEquals(['foo' => 'bar'], $this->searchResult->metadata()->toArray());
    }
}
