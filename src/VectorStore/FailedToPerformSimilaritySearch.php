<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

final class FailedToPerformSimilaritySearch extends \RuntimeException
{
    public static function because(\Throwable $exception): self
    {
        return new self(
            message: 'Failed to perform similarity search: ' . $exception->getMessage(),
            previous: $exception
        );
    }
}
