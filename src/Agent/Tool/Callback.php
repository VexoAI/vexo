<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

final class Callback implements Tool
{
    /**
     * @var callable(string): string
     */
    private $callable;

    /**
     * @param callable(string $input): string $callable
     */
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        callable $callable
    ) {
        $this->callable = $callable;
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
        return ($this->callable)($input);
    }
}
