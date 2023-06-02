<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Model\Completion\Model;

final class LanguageModelChainFactory
{
    public function __construct(
        private readonly Model $languageModel
    ) {
    }

    /**
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function createFromBlueprint(
        Blueprint $blueprint,
        array $inputMap = [],
        array $outputMap = []
    ): LanguageModelChain {
        return $this->create(
            promptRenderer: $blueprint->promptRenderer(),
            outputParser: $blueprint->outputParser(),
            stops: $blueprint->stops(),
            inputMap: $inputMap,
            outputMap: $outputMap
        );
    }

    /**
     * @param array<string>         $stops
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function create(
        Renderer $promptRenderer,
        OutputParser $outputParser,
        array $stops,
        array $inputMap = [],
        array $outputMap = []
    ): LanguageModelChain {
        return new LanguageModelChain(
            languageModel: $this->languageModel,
            promptRenderer: $promptRenderer,
            outputParser: $outputParser,
            stops: $stops,
            inputMap: $inputMap,
            outputMap: $outputMap
        );
    }
}
