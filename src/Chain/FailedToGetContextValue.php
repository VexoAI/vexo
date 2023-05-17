<?php

declare(strict_types=1);

namespace Vexo\Chain;

final class FailedToGetContextValue extends FailedToRunChain
{
    /**
     * @param array<string> $availableValues
     */
    public static function with(string $key, array $availableValues): self
    {
        return new self(
            sprintf(
                'Failed to get context value "%s". Available values: %s',
                $key,
                implode(', ', $availableValues)
            )
        );
    }
}
