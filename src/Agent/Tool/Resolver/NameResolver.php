<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Agent\Tool\Tool;
use Vexo\Agent\Tool\Tools;

final class NameResolver implements Resolver
{
    public function __construct(
        private readonly Tools $tools
    ) {
    }

    public function resolve(string $query, string $input): Tool
    {
        $nameToLookup = $this->normalizeName($query);

        foreach ($this->tools as $tool) {
            if ($this->normalizeName($tool->name()) === $nameToLookup) {
                return $tool;
            }
        }

        throw new FailedToResolveTool(sprintf('Failed to resolve tool %s', $query));
    }

    private function normalizeName(string $name): string
    {
        return trim(strtolower($name));
    }
}
