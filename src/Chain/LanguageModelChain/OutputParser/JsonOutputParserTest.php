<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonOutputParser::class)]
final class JsonOutputParserTest extends TestCase
{
    private object $schema;
    private JsonOutputParser $outputParser;

    protected function setUp(): void
    {
        $this->schema = (object) [
            'type' => 'object',
            'properties' => (object) [
                'name' => (object) [
                    'type' => 'string'
                ]
            ]
        ];
        $this->outputParser = JsonOutputParser::createWithSchema($this->schema);
    }

    public function testParseValidJson(): void
    {
        $input = "```json\n{\"name\": \"John Doe\"}\n```";
        $expected = ['name' => 'John Doe'];
        $this->assertEquals($expected, $this->outputParser->parse($input));
    }

    public function testParseInvalidJsonFormat(): void
    {
        $input = "```json\n{\"name\": John Doe}\n```";
        $this->expectException(FailedToParseOutput::class);
        $this->expectExceptionMessage('Failed to decode JSON: Syntax error');
        $this->outputParser->parse($input);
    }

    public function testParseInvalidJsonSchema(): void
    {
        $input = "```json\n{\"name\": 123}\n```";
        $this->expectException(FailedToParseOutput::class);
        $this->expectExceptionMessageMatches('/Failed to validate JSON/');
        $this->outputParser->parse($input);
    }

    public function testParseMissingDelimiters(): void
    {
        $input = '{"name": "John Doe"}';
        $this->expectException(FailedToParseOutput::class);
        $this->expectExceptionMessage('Failed to extract JSON from output');
        $this->outputParser->parse($input);
    }
}
