<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

final class FailedToEmbedText extends \RuntimeException
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Model failed to generate embedding: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
