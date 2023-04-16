<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

interface Command
{
    public static function fromArray(array $arguments): Command;

    public function arguments(): array;
}