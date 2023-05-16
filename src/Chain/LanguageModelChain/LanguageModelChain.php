<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Model\Language\LanguageModel;

final class LanguageModelChain implements Chain
{
    /**
     * @param array<string> $stops
     */
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly Renderer $promptRenderer,
        private readonly ?OutputParser $outputParser = null,
        private readonly array $stops = []
    ) {
    }

    public function run(Context $context): void
    {
        $result = $this->languageModel->generate(
            $this->promptRenderer->render($context),
            $this->stops
        );

        $context->put('generation', $result->generations()[0]);

        if ( ! $this->outputParser instanceof OutputParser) {
            return;
        }

        $parsed = $this->outputParser->parse($result->generations()[0]);
        foreach ($parsed as $key => $value) {
            $context->put($key, $value);
        }
    }
}
