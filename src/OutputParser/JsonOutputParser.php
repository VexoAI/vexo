<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

class JsonOutputParser implements OutputParser
{
    public const INSTRUCTIONS = <<<INSTRUCTIONS
        The output should be a markdown code snippet formatted in the following schema, including the leading and trailing "```json" and "```":

        ```json
        {{schema}}
        ```
        INSTRUCTIONS;

    public function __construct(
        private Validator $validator,
        private string $schema
    ) {
    }

    public function formatInstructions(): string
    {
        return str_replace('{{schema}}', $this->schema, self::INSTRUCTIONS);
    }

    public function parse(string $text): mixed
    {
        $decoded = $this->decodeJson($this->extractJsonString($text));

        try {
            $this->validator->validate(
                $decoded,
                json_decode($this->schema),
                Constraint::CHECK_MODE_COERCE_TYPES | Constraint::CHECK_MODE_APPLY_DEFAULTS | Constraint::CHECK_MODE_VALIDATE_SCHEMA | Constraint::CHECK_MODE_EXCEPTIONS
            );
        } catch (ValidationException $e) {
            throw new SorryFailedToParseOutput('Failed to validate JSON: ' . $e->getMessage());
        }

        return $decoded;
    }

    private function extractJsonString(string $text): string
    {
        $startDelimiter = '```json';
        $endDelimiter = '```';

        $startPosition = strpos($text, $startDelimiter) + \strlen($startDelimiter);
        $endPosition = strpos($text, $endDelimiter, $startPosition);

        /* @phpstan-ignore-next-line */
        if ($startPosition === false || $endPosition === false) {
            throw new SorryFailedToParseOutput('Failed to extract JSON from output');
        }

        // Extract the JSON string using the positions
        return trim(substr($text, $startPosition, $endPosition - $startPosition));
    }

    private function decodeJson(string $json): mixed
    {
        $decoded = json_decode($json);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new SorryFailedToParseOutput('Failed to decode JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
