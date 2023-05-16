<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;

#[CoversClass(ReasonAndAct::class)]
final class ReasonAndActTest extends TestCase
{
    public function testBasicProperties(): void
    {
        $blueprint = new ReasonAndAct();

        $this->assertInstanceOf(Renderer::class, $blueprint->promptRenderer());
        $this->assertInstanceOf(OutputParser::class, $blueprint->outputParser());
        $this->assertEquals(['Observation:'], $blueprint->stops());
    }

    #[DataProvider('provideOutputsAndExpectedParseResults')]
    public function testOutputParserParsesOutput(string $output, array $expected): void
    {
        $blueprint = new ReasonAndAct();

        $parsed = $blueprint->outputParser()->parse($output);

        foreach ($expected as $key => $value) {
            $this->assertEquals($value, $parsed[$key]);
        }
    }

    public static function provideOutputsAndExpectedParseResults(): array
    {
        return [
            'next step' => [
                "Some thought\nAction: Google\nAction input: My search",
                ['thought' => 'Some thought', 'action' => 'Google', 'input' => 'My search']
            ],
            'next step without action input' => [
                "Some thought\nAction: Google\nAction input:",
                ['thought' => 'Some thought', 'action' => 'Google', 'input' => '']
            ],
            'next step with mixed case' => [
                "Some thought\nACTION: Google\nACTION INPUT: My search",
                ['thought' => 'Some thought', 'action' => 'Google', 'input' => 'My search']
            ],
            'conclusion' => [
                "Some final thought\nFinal answer: My conclusion",
                ['final_thought' => 'Some final thought', 'final_answer' => 'My conclusion']
            ],
            'conclusion with mixed case' => [
                "Some final thought\nFINAL ANSWER: My conclusion",
                ['final_thought' => 'Some final thought', 'final_answer' => 'My conclusion']
            ],
        ];
    }
}
