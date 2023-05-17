<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use Vexo\Chain\FailedToRunChain;

final class FailedToRenderPrompt extends FailedToRunChain
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Failed to render prompt: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
