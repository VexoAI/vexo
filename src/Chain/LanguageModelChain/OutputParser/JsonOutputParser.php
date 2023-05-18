<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

final class JsonOutputParser implements OutputParser
{
    private const START_DELIMITER = '```json';
    private const END_DELIMITER = '```';

    public function __construct(
        private readonly Validator $validator,
        private readonly string $schema
    ) {
    }

    public function parse(string $text): array
    {
        try {
            $decoded = json_decode($this->extractJsonString($text), null, 512, \JSON_THROW_ON_ERROR);

            $this->validator->validate(
                $decoded,
                json_decode($this->schema, null, 512, \JSON_THROW_ON_ERROR),
                Constraint::CHECK_MODE_COERCE_TYPES | Constraint::CHECK_MODE_APPLY_DEFAULTS | Constraint::CHECK_MODE_VALIDATE_SCHEMA | Constraint::CHECK_MODE_EXCEPTIONS
            );
        } catch (\JsonException $e) {
            throw new FailedToParseOutput(message: 'Failed to decode JSON: ' . $e->getMessage(), previous: $e);
        } catch (ValidationException $e) {
            throw new FailedToParseOutput(message: 'Failed to validate JSON: ' . $e->getMessage(), previous: $e);
        }

        return (array) $decoded;
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
}
