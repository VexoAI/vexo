<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use Vexo\Chain\LanguageModelChain\Blueprint;

final class QuestionAnswerBlueprint implements Blueprint
{
    public function promptTemplate(): string
    {
        return 'answer-question-about-context.twig';
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
