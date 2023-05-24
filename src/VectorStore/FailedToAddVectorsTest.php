<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToAddVectors::class)]
final class FailedToAddVectorsTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToAddVectors = FailedToAddVectors::because($exception);

        $this->assertSame(
            'Failed to add vectors: Some exception',
            $failedToAddVectors->getMessage()
        );
        $this->assertSame($exception, $failedToAddVectors->getPrevious());
    }
}
