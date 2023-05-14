<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

final class FailedToResolveTool extends \RuntimeException
{
    public static function for(string $query): self
    {
        return new self(sprintf('Failed to resolve tool "%s"', $query));
    }
}
