<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

class RegexOutputParser implements OutputParser
{
    private const INSTRUCTIONS = <<<INSTRUCTIONS
        The output should be text which matches the following PCRE regex, including the leading and trailing "```output" and "```":

        ```output
        {{regex}}
        ```
        INSTRUCTIONS;

    public function __construct(
        private readonly string $regex
    ) {
    }

    public function formatInstructions(): string
    {
        return str_replace('{{regex}}', $this->regex, self::INSTRUCTIONS);
    }

    public function parse(string $text): mixed
    {
        $matches = [];

        $result = @preg_match($this->regex, $text, $matches);
        if ($result === false) {
            throw new SorryFailedToParseOutput(sprintf('Failed to parse output. Invalid regular expression: %s', $this->regex));
        }

        if ($result === 0) {
            throw new SorryFailedToParseOutput('Failed to parse output. No matches found.');
        }

        return $matches;
    }
}
