<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\OutputParser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegexOutputParser::class)]
final class RegexOutputParserTest extends TestCase
{
    public function testParse(): void
    {
        $outputParser = new RegexOutputParser('/Action: (?P<action>.*?)\nAction input: (?P<input>.*)/');

        $matches = $outputParser->parse("Action: some action\nAction input: some input");
        $this->assertSame('some action', $matches['action']);
        $this->assertSame('some input', $matches['input']);
    }

    public function testParseWithInvalidRegex(): void
    {
        $outputParser = new RegexOutputParser('/^Hello, (?P<very_invalid.*)!$/');

        $this->expectException(FailedToParseOutput::class);
        $this->expectExceptionMessage('Invalid regular expression');
        $outputParser->parse('Hello, John Doe!');
    }

    public function testParseWithoutMatches(): void
    {
        $outputParser = new RegexOutputParser('/^Hello, (?P<name>.*)!$/');

        $this->expectException(FailedToParseOutput::class);
        $this->expectExceptionMessage('No matches found');
        $outputParser->parse('This will not match');
    }
}
