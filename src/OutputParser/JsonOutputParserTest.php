<?php

declare(strict_types=1);

namespace Vexo\OutputParser;

use JsonSchema\Validator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonOutputParser::class)]
final class JsonOutputParserTest extends TestCase
{
    private Validator $validator;
    private string $schema;
    private JsonOutputParser $outputParser;

    protected function setUp(): void
    {
        $this->validator = new Validator();
        $this->schema = '{"type": "object", "properties": {"name": {"type": "string"}}}';
        $this->outputParser = new JsonOutputParser($this->validator, $this->schema);
    }

    public function testFormatInstructions(): void
    {
        $expected = 'The output should be a markdown code snippet formatted in the following schema, '
            . "including the leading and trailing \"```json\" and \"```\":\n\n```json\n{$this->schema}\n```";
        $this->assertSame($expected, $this->outputParser->formatInstructions());
    }

    public function testParseValidJson(): void
    {
        $input = "```json\n{\"name\": \"John Doe\"}\n```";
        $expected = (object) ['name' => 'John Doe'];
        $this->assertEquals($expected, $this->outputParser->parse($input));
    }

    public function testParseInvalidJsonFormat(): void
    {
        $input = "```json\n{\"name\": John Doe}\n```";
        $this->expectException(SorryFailedToParseOutput::class);
        $this->expectExceptionMessage('Failed to decode JSON: Syntax error');
        $this->outputParser->parse($input);
    }

    public function testParseInvalidJsonSchema(): void
    {
        $input = "```json\n{\"name\": 123}\n```";
        $this->expectException(SorryFailedToParseOutput::class);
        $this->expectExceptionMessageMatches('/Failed to validate JSON/');
        $this->outputParser->parse($input);
    }

    public function testParseMissingDelimiters(): void
    {
        $input = '{"name": "John Doe"}';
        $this->expectException(SorryFailedToParseOutput::class);
        $this->expectExceptionMessage('Failed to extract JSON from output');
        $this->outputParser->parse($input);
    }
}
