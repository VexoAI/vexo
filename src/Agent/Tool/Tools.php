<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Tool>
 */
final class Tools extends AbstractCollection
{
    public function getType(): string
    {
        return Tool::class;
    }

    public function resolve(string $query): Tool
    {
        $nameToLookup = $this->normalizeName($query);

        foreach ($this as $tool) {
            if ($this->normalizeName($tool->name()) === $nameToLookup) {
                return $tool;
            }
        }

        throw FailedToResolveTool::for($query, array_map(fn (Tool $tool): string => $tool->name(), $this->toArray()));
    }

    private function normalizeName(string $name): string
    {
        return trim(strtolower($name));
    }
}
