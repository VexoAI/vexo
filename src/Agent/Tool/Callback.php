<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

final class Callback implements Tool
{
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly mixed $callable
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function run(string $input): string
    {
        return (string) \call_user_func($this->callable, $input);
    }
}
