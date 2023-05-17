<?php

declare(strict_types=1);

namespace Vexo\Chain;

final class FailedToValidateContextValue extends FailedToRunChain
{
    public static function because(string $key, \Throwable $exception): self
    {
        return new self(
            sprintf(
                'Failed to validate context value "%s": %s',
                $key,
                $exception->getMessage()
            ),
            previous: $exception
        );
    }
}
