<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

interface OutputParser
{
    public function parse(string $text): array;
}
