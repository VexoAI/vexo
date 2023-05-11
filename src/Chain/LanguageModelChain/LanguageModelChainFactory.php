<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\LanguageModel\LanguageModel;
use Vexo\LanguageModel\Prompt\BasicPromptTemplate;

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
            stops: $blueprint->stops(),
            inputKeys: $blueprint->inputKeys(),
            outputKey: $blueprint->outputKey()
        );
    }

    /**
     * @param array<string> $stops
     * @param array<string> $inputKeys
     */
    public function create(string $promptTemplate, array $stops, array $inputKeys, string $outputKey): LanguageModelChain
    {
        return new LanguageModelChain(
            languageModel: $this->languageModel,
            promptTemplate: new BasicPromptTemplate($promptTemplate, $inputKeys),
            inputKeys: $inputKeys,
            outputKey: $outputKey,
            stops: $stops
        );
    }
}
