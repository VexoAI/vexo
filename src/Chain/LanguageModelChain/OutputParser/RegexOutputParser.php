<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

class RegexOutputParser implements OutputParser
{
    public function __construct(
        private readonly string $regex
    ) {
    }

    public function parse(string $text): array
    {
        $matches = [];

        $result = @preg_match($this->regex, $text, $matches);
        if ($result === false) {
            throw new FailedToParseOutput(sprintf('Failed to parse output. Invalid regular expression: %s', $this->regex));
        }

        if ($result === 0) {
            throw new FailedToParseOutput('Failed to parse output. No matches found.');
        }

        return array_filter($matches, fn ($key): bool => is_string($key), ARRAY_FILTER_USE_KEY);
    }
}
