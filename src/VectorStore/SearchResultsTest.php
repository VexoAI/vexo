<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SearchResults::class)]
final class SearchResultsTest extends TestCase
{
    public function testGetType(): void
    {
        $searchResults = new SearchResults();

        $this->assertSame(SearchResult::class, $searchResults->getType());
    }
}
