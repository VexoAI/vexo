<?php

declare(strict_types=1);

namespace Vexo\Weave\Tool;

final class CallableTool implements Tool
{
    public function __construct(
        private string $name,
        private string $description,
        private mixed $callable
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
        return (string) call_user_func($this->callable, $input);
    }
}
