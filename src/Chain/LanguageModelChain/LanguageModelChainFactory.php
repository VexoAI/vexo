<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\LanguageModelChain\Prompt\TwigRenderer;
use Vexo\LanguageModel\LanguageModel;

final class LanguageModelChainFactory
{
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly string $promptTemplatePath = __DIR__ . '/Prompt/templates'
    ) {
    }

    public function createFromBlueprint(Blueprint $blueprint): LanguageModelChain
    {
        return $this->create(
            promptTemplate: $blueprint->promptTemplate(),
            requiredContextValues: $blueprint->requiredContextValues(),
            stops: $blueprint->stops()
        );
    }

    /**
     * @param array<string> $requiredContextValues
     * @param array<string> $stops
     */
    public function create(
        string $promptTemplate,
        array $requiredContextValues,
        array $stops
    ): LanguageModelChain {
        return new LanguageModelChain(
            languageModel: $this->languageModel,
            promptRenderer: TwigRenderer::createWithFilesystemLoader($promptTemplate, $this->promptTemplatePath),
            requiredContextValues: $requiredContextValues,
            stops: $stops
        );
    }
}
