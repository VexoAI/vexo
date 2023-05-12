<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Webmozart\Assert\InvalidArgumentException;

final class RequiredContextValueForChainHasIncorrectType extends FailedToRunChain
{
    public static function with(
        string $name,
        string $chainClass,
        string $identifier,
        InvalidArgumentException $exception
    ): self {
        return new self(
            message: sprintf(
                'The required context value "%s" for chain %s (%s) has incorrect type: %s',
                $name,
                $chainClass,
                $identifier,
                $exception->getMessage()
            ),
            previous: $exception
        );
    }
}
