<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Attribute\RequiresContextValuesMethod;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\LanguageModel\LanguageModel;
use Vexo\LanguageModel\Prompt\PromptTemplate;

#[RequiresContextValuesMethod('requiredContextValues')]
final class LanguageModelChain implements Chain
{
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly PromptTemplate $promptTemplate,
        private readonly array $stops = []
    ) {
    }

    public function requiredContextValues(): array
    {
        return array_fill_keys($this->promptTemplate->variables(), 'mixed');
    }

    public function run(Context $context): void
    {
        $response = $this->languageModel->generate(
            $this->promptTemplate->render($context->toArray()),
            ...$this->stops
        );

        $context->put('text', (string) $response->completions());
    }
}
