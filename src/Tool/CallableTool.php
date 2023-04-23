<?php

declare(strict_types=1);

namespace Vexo\Weave\Tool;

final class CallableTool extends BaseTool
{
    public function __construct(
        protected string $name,
        protected string $description,
        private mixed $callable
    ) {
    }

    protected function call(string $input): string
    {
        return (string) call_user_func($this->callable, $input);
    }
}
