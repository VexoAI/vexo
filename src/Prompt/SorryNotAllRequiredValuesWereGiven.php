<?php

declare(strict_types=1);

namespace Vexo\Weave\Prompt;

final class SorryNotAllRequiredValuesWereGiven extends \InvalidArgumentException
{
    public static function with(array $variables): self
    {
        return new self(
            sprintf(
                'Sorry, not all required values were given. Missing: %s',
                implode(', ', $variables)
            )
        );
    }
}
