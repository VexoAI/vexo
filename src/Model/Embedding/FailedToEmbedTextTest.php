<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToEmbedText::class)]
final class FailedToEmbedTextTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToEmbedText = FailedToEmbedText::because($exception);

        $this->assertSame(
            'Model failed to generate embedding: Some exception',
            $failedToEmbedText->getMessage()
        );
        $this->assertSame($exception, $failedToEmbedText->getPrevious());
    }
}
