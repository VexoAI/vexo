<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\LanguageModelChain\Prompt\BasicPromptTemplate;
use Vexo\LanguageModel\LanguageModel;

final class LanguageModelChainFactory
{
    public function __construct(
        private readonly LanguageModel $languageModel
    ) {
    }

    public function createFromBlueprint(Blueprint $blueprint): LanguageModelChain
    {
        return $this->create(
            promptTemplate: $blueprint->promptTemplate(),
            promptVariables: $blueprint->promptVariables(),
            stops: $blueprint->stops()
        );
    }

    /**
     * @param array<string> $promptVariables
     * @param array<string> $stops
     */
    public function create(string $promptTemplate, array $promptVariables, array $stops): LanguageModelChain
    {
        return new LanguageModelChain(
            languageModel: $this->languageModel,
            promptTemplate: new BasicPromptTemplate($promptTemplate, $promptVariables),
            stops: $stops
        );
    }
}
