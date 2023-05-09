<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

class JsonOutputParser implements OutputParser
{
    private const INSTRUCTIONS = <<<INSTRUCTIONS
        The output should be a markdown code snippet formatted in the following schema, including the leading and trailing "```json" and "```":

        ```json
        {{schema}}
        ```
        INSTRUCTIONS;

    private const START_DELIMITER = '```json';
    private const END_DELIMITER = '```';

    public function __construct(
        private readonly Validator $validator,
        private readonly string $schema
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
                json_decode($this->schema, null, 512, \JSON_THROW_ON_ERROR),
                Constraint::CHECK_MODE_COERCE_TYPES | Constraint::CHECK_MODE_APPLY_DEFAULTS | Constraint::CHECK_MODE_VALIDATE_SCHEMA | Constraint::CHECK_MODE_EXCEPTIONS
            );
        } catch (ValidationException $e) {
            throw new FailedToParseOutput('Failed to validate JSON: ' . $e->getMessage());
        }

        return $decoded;
    }

    private function extractJsonString(string $text): string
    {
        $startPosition = strpos($text, self::START_DELIMITER);
        $endPosition = strpos($text, self::END_DELIMITER, $startPosition + \strlen(self::START_DELIMITER));

        if ($startPosition === false || $endPosition === false) {
            throw new FailedToParseOutput('Failed to extract JSON from output');
        }

        $startPosition += \strlen(self::START_DELIMITER);

        // Extract the JSON string using the positions
        return trim(substr($text, $startPosition, $endPosition - $startPosition));
    }

    private function decodeJson(string $json): mixed
    {
        $decoded = json_decode($json);

        if (json_last_error() !== \JSON_ERROR_NONE) {
            throw new FailedToParseOutput('Failed to decode JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }
}
