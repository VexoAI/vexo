<?php

declare(strict_types=1);

namespace Vexo\Prompt;

final class FailedToRenderPrompt extends \InvalidArgumentException
{
    public static function with(array $variables): self
    {
        return new self(
            sprintf(
                'Failed to render prompt. Missing values: %s',
                implode(', ', $variables)
            )
        );
    }
}
