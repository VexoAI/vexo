<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

final class FailedToGenerateResult extends \RuntimeException
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Model failed to generate a result: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
