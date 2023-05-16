<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\ContextValueMapperBehavior;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Model\Language\LanguageModel;

final class LanguageModelChain implements Chain
{
    use ContextValueMapperBehavior;

    private const OUTPUT_GENERATION = 'generation';

    /**
     * @param array<string>         $stops
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly Renderer $promptRenderer,
        private readonly ?OutputParser $outputParser = null,
        private readonly array $stops = [],
        private readonly array $inputMap = [],
        private readonly array $outputMap = []
    ) {
    }

    public function run(Context $context): void
    {
        $promptContext = clone $context;
        foreach ($this->inputMap as $from => $to) {
            $promptContext->put($from, $promptContext->get($to));
        }

        $result = $this->languageModel->generate(
            $this->promptRenderer->render($promptContext),
            $this->stops
        );

        $this->put($context, self::OUTPUT_GENERATION, $result->generations()[0]);

        if ( ! $this->outputParser instanceof OutputParser) {
            return;
        }

        $parsed = $this->outputParser->parse($result->generations()[0]);
        foreach ($parsed as $key => $value) {
            $this->put($context, $key, $value);
        }
    }
}
