<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Agent\Tool\Tool;

final class NameResolver extends BaseResolver
{
    protected function lookup(string $query, string $input): Tool
    {
        $nameToLookup = $this->normalizeName($query);

        foreach ($this->tools as $tool) {
            if ($this->normalizeName($tool->name()) === $nameToLookup) {
                return $tool;
            }
        }

        throw new \RuntimeException('Name not found');
    }

    private function normalizeName(string $name): string
    {
        return trim(strtolower($name));
    }
}
