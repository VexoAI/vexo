<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

final class FailedToAddVectors extends \RuntimeException
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Failed to add vectors: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
