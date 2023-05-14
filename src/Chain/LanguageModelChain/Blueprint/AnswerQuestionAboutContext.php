<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use Vexo\Chain\LanguageModelChain\Blueprint;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Chain\LanguageModelChain\Prompt\TwigRenderer;

final class AnswerQuestionAboutContext implements Blueprint
{
    public function promptRenderer(): Renderer
    {
        return TwigRenderer::createWithFilesystemLoader(
            'answer-question-about-context.twig',
            __DIR__ . '/../Prompt/templates'
        );
    }

    public function outputParser(): OutputParser
    {
        return new RegexOutputParser('/^(?<answer>.*)$/');
    }

    public function requiredContextValues(): array
    {
        return ['context', 'question'];
    }

    /**
     * @return array<string>
     */
    public function stops(): array
    {
        return [];
    }
}
