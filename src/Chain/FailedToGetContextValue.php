<?php

declare(strict_types=1);

namespace Vexo\Chain;

final class FailedToGetContextValue extends FailedToRunChain
{
    public static function with(string $key, Context $context): self
    {
        return new self(
            sprintf(
                'Failed to get context value "%s". Available values: %s',
                $key,
                implode(', ', $context->keys())
            )
        );
    }
}
