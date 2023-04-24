<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

interface OutputParser
{
    public function formatInstructions(): string;

    public function parse(string $text): mixed;
}
