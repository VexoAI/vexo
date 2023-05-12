<?php

declare(strict_types=1);

namespace Vexo\Chain;

final class FailedToFindRequiredContextValueForChain extends FailedToRunChain
{
    public static function with(string $name, string $chainClass, string $identifier): self
    {
        return new self(
            sprintf(
                'Failed to find required context value "%s" for chain %s (%s)',
                $name,
                $chainClass,
                $identifier
            )
        );
    }
}
