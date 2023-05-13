<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Attribute\RequiresContextValuesMethod;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\LanguageModel\LanguageModel;

#[RequiresContextValuesMethod('requiredContextValues')]
final class LanguageModelChain implements Chain
{
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly Renderer $promptRenderer,
        private readonly array $requiredContextValues = [],
        private readonly array $stops = []
    ) {
    }

    public function requiredContextValues(): array
    {
        return array_fill_keys($this->requiredContextValues, 'mixed');
    }

    public function run(Context $context): void
    {
        $response = $this->languageModel->generate(
            $this->promptRenderer->render($context),
            ...$this->stops
        );

        $context->put('text', (string) $response->completions());
    }
}
