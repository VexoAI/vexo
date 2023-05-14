<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

interface OutputParser
{
    /**
     * @return array<string, string>
     */
    public function parse(string $text): array;
}
