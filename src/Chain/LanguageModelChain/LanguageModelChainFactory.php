<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Model\Language\LanguageModel;

final class LanguageModelChainFactory
{
    public function __construct(
        private readonly LanguageModel $languageModel
    ) {
    }

    public function createFromBlueprint(Blueprint $blueprint): LanguageModelChain
    {
        return $this->create(
            promptRenderer: $blueprint->promptRenderer(),
            outputParser: $blueprint->outputParser(),
            requiredContextValues: $blueprint->requiredContextValues(),
            stops: $blueprint->stops()
        );
    }

    /**
     * @param array<string> $requiredContextValues
     * @param array<string> $stops
     */
    public function create(
        Renderer $promptRenderer,
        OutputParser $outputParser,
        array $requiredContextValues,
        array $stops
    ): LanguageModelChain {
        return new LanguageModelChain(
            languageModel: $this->languageModel,
            promptRenderer: $promptRenderer,
            outputParser: $outputParser,
            requiredContextValues: $requiredContextValues,
            stops: $stops
        );
    }
}
