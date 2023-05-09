<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;

abstract class BaseTool implements Tool, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        protected string $name,
        protected string $description
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
        $this->emit(new ToolStarted($input));

        $output = $this->call($input);

        $this->emit(new ToolFinished($input, $output));

        return $output;
    }

    abstract protected function call(string $input): string;
}
