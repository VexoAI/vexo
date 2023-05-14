<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Attribute\RequiresContextValuesMethod;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\LanguageModel\LanguageModel;

#[RequiresContextValuesMethod('requiredContextValues')]
final class LanguageModelChain implements Chain
{
    /**
     * @param array<string> $requiredContextValues
     * @param array<string> $stops
     */
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly Renderer $promptRenderer,
        private readonly OutputParser $outputParser,
        private readonly array $requiredContextValues = [],
        private readonly array $stops = []
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function requiredContextValues(): array
    {
        return array_fill_keys($this->requiredContextValues, 'mixed');
    }

    public function run(Context $context): void
    {
        $result = $this->languageModel->generate(
            $this->promptRenderer->render($context),
            $this->stops
        );

        $context->put('completions', $result->generations()[0]);

        $parsed = $this->outputParser->parse($result->generations()[0]);
        foreach ($parsed as $key => $value) {
            $context->put($key, $value);
        }
    }
}
