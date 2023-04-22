<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent;

final class Action
{
    public function __construct(
        private string $tool,
        private array $input
    ) {
    }

    public function tool(): string
    {
        return $this->tool;
    }

    public function input(): array
    {
        return $this->input;
    }
}
