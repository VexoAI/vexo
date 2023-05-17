<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToEvaluateCondition::class)]
final class FailedToEvaluateConditionTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToEvaluateCondition = FailedToEvaluateCondition::because($exception);

        $this->assertSame(
            'Failed to evaluate condition: Some exception',
            $failedToEvaluateCondition->getMessage()
        );
        $this->assertSame($exception, $failedToEvaluateCondition->getPrevious());
    }
}
