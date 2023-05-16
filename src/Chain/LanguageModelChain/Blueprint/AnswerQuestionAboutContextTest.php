<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;

#[CoversClass(AnswerQuestionAboutContext::class)]
final class AnswerQuestionAboutContextTest extends TestCase
{
    public function testBasicProperties(): void
    {
        $blueprint = new AnswerQuestionAboutContext();

        $this->assertInstanceOf(Renderer::class, $blueprint->promptRenderer());
        $this->assertInstanceOf(OutputParser::class, $blueprint->outputParser());
        $this->assertEmpty($blueprint->stops());
    }

    public function testOutputParserParsesOutput(): void
    {
        $blueprint = new AnswerQuestionAboutContext();

        $this->assertEquals(
            ['answer' => 'Some answer'],
            $blueprint->outputParser()->parse('Some answer')
        );
    }
}
