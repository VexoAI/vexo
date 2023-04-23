<?php

declare(strict_types=1);

namespace Vexo\Weave\Tool;

use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;

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
        $this->eventDispatcher()->dispatch(
            (new ToolStarted($input))->for($this)
        );

        $output = $this->call($input);

        $this->eventDispatcher()->dispatch(
            (new ToolFinished($input, $output))->for($this)
        );

        return $output;
    }

    abstract protected function call(string $input): string;
}
