<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\LanguageModelChain\Blueprint\AnswerQuestionAboutContext;
use Vexo\Chain\LanguageModelChain\Blueprint\ReasonAndAct;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;

#[CoversClass(AnswerQuestionAboutContext::class)]
#[CoversClass(ReasonAndAct::class)]
final class BlueprintTest extends TestCase
{
    private const BLUEPRINT_CLASSES = [
        AnswerQuestionAboutContext::class,
        ReasonAndAct::class
    ];

    public static function blueprintProvider(): array
    {
        return array_map(
            fn (string $blueprintClass): array => [new $blueprintClass()],
            self::BLUEPRINT_CLASSES
        );
    }

    #[DataProvider('blueprintProvider')]
    public function testBlueprint(Blueprint $blueprint): void
    {
        $this->assertInstanceOf(Renderer::class, $blueprint->promptRenderer());
        $this->assertInstanceOf(OutputParser::class, $blueprint->outputParser());
        $this->assertContainsOnly('string', $blueprint->requiredContextValues());
        $this->assertContainsOnly('string', $blueprint->stops());
    }
}
