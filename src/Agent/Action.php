<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Action
{
    public function __construct(
        private readonly string $tool,
        private readonly string $input
    ) {
    }

    public function tool(): string
    {
        return $this->tool;
    }

    public function input(): string
    {
        return $this->input;
    }
}
