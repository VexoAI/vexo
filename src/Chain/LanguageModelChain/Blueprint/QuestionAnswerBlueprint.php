<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Blueprint;

use Vexo\Chain\LanguageModelChain\Blueprint;

final class QuestionAnswerBlueprint implements Blueprint
{
    public function promptTemplate(): string
    {
        return 'Use the following pieces of context to answer the question at the end. '
            . 'If you don\'t know the answer, just say that you don\'t know. Don\'t try to make up an answer.'
            . "\n\n"
            . '{{context}}'
            . "\n\n"
            . 'Question: {{question}}'
            . "\n"
            . 'Helpful Answer: ';
    }

    /**
     * @return array<string>
     */
    public function stops(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function inputKeys(): array
    {
        return ['context', 'question'];
    }

    public function outputKey(): string
    {
        return 'answer';
    }
}
