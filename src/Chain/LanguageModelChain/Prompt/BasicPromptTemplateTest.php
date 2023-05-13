<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BasicPromptTemplate::class)]
final class BasicPromptTemplateTest extends TestCase
{
    public function testRender(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'What is the capital of {{country}}?',
            ['country']
        );

        $this->assertEquals(
            'What is the capital of France?',
            $promptTemplate->render(['country' => 'France'])
        );
    }

    public function testRenderReplacesInCorrectOrder(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'Roses are {{first_color}}, violets are {{second_color}}.',
            ['first_color', 'second_color']
        );

        $this->assertEquals(
            'Roses are Red, violets are Blue.',
            $promptTemplate->render(['second_color' => 'Blue', 'first_color' => 'Red'])
        );
    }

    public function testRenderValidatesValues(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'What is the capital of {{country}}?',
            ['country']
        );

        $this->expectException(FailedToRenderPrompt::class);
        $promptTemplate->render(['foo' => 'bar']);
    }

    public function testVariables(): void
    {
        $promptTemplate = new BasicPromptTemplate(
            'What is the capital of {{country}}?',
            ['country']
        );

        $this->assertEquals(['country'], $promptTemplate->variables());
    }
}
