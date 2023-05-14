<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

final class FailedToResolveTool extends \RuntimeException
{
    /**
     * @param array<string> $availableTools
     */
    public static function for(string $query, array $availableTools): self
    {
        return new self(sprintf('Failed to resolve tool "%s"; available tools: %s', $query, implode(', ', $availableTools)));
    }
}
