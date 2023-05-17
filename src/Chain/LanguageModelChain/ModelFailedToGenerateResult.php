<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\FailedToRunChain;

final class ModelFailedToGenerateResult extends FailedToRunChain
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Model failed to generate result: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
