<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

class RegexOutputParser implements OutputParser
{
    public function __construct(
        private string $regex,
        private string $formatInstructions = ''
    ) {
    }

    public function formatInstructions(): string
    {
        return $this->formatInstructions;
    }

    public function parse(string $text): mixed
    {
        $matches = [];

        $result = @preg_match($this->regex, $text, $matches);
        if ($result === false) {
            throw new SorryFailedToParseOutput(
                sprintf('Failed to parse output. Invalid regular expression: %s', $this->regex)
            );
        }

        if ($result === 0) {
            throw new SorryFailedToParseOutput('Failed to parse output. No matches found.');
        }

        return $matches;
    }
}
