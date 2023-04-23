<?php

declare(strict_types=1);

namespace Vexo\Weave\Tool;

interface Tool
{
    public function name(): string;

    public function description(): string;

    public function run(string $input): string;
}
