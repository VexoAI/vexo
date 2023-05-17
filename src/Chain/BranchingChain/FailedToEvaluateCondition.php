<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use Vexo\Chain\FailedToRunChain;

final class FailedToEvaluateCondition extends FailedToRunChain
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Failed to evaluate condition: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
