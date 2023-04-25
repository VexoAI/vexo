<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Action;
use Vexo\Agent\Finish;

#[CoversClass(OutputParser::class)]
final class OutputParserTest extends TestCase
{
    private OutputParser $outputParser;

    public function setUp(): void
    {
        $this->outputParser = new OutputParser();
    }

    public function testFormatInstructions(): void
    {
        $this->assertSame(Prompt::FORMAT_INSTRUCTIONS, $this->outputParser->formatInstructions());
    }

    public function testParseAction(): void
    {
        $result = $this->outputParser->parse("I must search the web.\nAction: Google Search\nAction Input: Meaning of life");

        $this->assertInstanceOf(Action::class, $result);
        $this->assertEquals('Google Search', $result->tool());
        $this->assertEquals('Meaning of life', $result->input());
    }

    public function testParseFinalAnswer(): void
    {
        $result = $this->outputParser->parse("I finally know the answer!\nFinal Answer: 42");

        $this->assertInstanceOf(Finish::class, $result);
        $this->assertEquals('42', $result->results()['result']);
    }
}
