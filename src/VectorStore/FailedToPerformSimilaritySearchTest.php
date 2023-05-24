<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToPerformSimilaritySearch::class)]
final class FailedToPerformSimilaritySearchTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToPerformSimilaritySearch = FailedToPerformSimilaritySearch::because($exception);

        $this->assertSame(
            'Failed to perform similarity search: Some exception',
            $failedToPerformSimilaritySearch->getMessage()
        );
        $this->assertSame($exception, $failedToPerformSimilaritySearch->getPrevious());
    }
}
