<?php

declare(strict_types=1);

namespace Vexo\Model\Completion;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToGenerateResult::class)]
final class FailedToGenerateResultTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToGenerateResult = FailedToGenerateResult::because($exception);

        $this->assertSame(
            'Model failed to generate a result: Some exception',
            $failedToGenerateResult->getMessage()
        );
        $this->assertSame($exception, $failedToGenerateResult->getPrevious());
    }
}
