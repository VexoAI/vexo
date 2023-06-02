<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\ContextAssert;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Model\Completion\Model;
use Vexo\Model\Completion\Result;

final class LanguageModelChain implements Chain
{
    private const INPUT_PROMPT = 'prompt';
    private const OUTPUT_GENERATION = 'generation';

    /**
     * @param array<string>         $stops
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function __construct(
        private readonly Model $languageModel,
        private readonly ?Renderer $promptRenderer = null,
        private readonly ?OutputParser $outputParser = null,
        private readonly array $stops = [],
        private readonly array $inputMap = [],
        private readonly array $outputMap = []
    ) {
    }

    public function run(Context $context): void
    {
        $prompt = $this->createPrompt($context);

        try {
            $result = $this->languageModel->generate($prompt, $this->stops);
        } catch (\Throwable $exception) {
            throw ModelFailedToGenerateResult::because($exception);
        }

        $this->putResult($context, $result);
    }

    private function createPrompt(Context $context): string
    {
        if ( ! $this->promptRenderer instanceof Renderer) {
            $prompt = $context->get($this->inputMap[self::INPUT_PROMPT] ?? self::INPUT_PROMPT);
            ContextAssert::stringNotEmpty($prompt);

            return $prompt;
        }

        $promptContext = clone $context;
        foreach ($this->inputMap as $from => $to) {
            $promptContext->put($from, $promptContext->get($to));
        }

        return $this->promptRenderer->render($promptContext);
    }

    private function putResult(Context $context, Result $result): void
    {
        $context->put($this->outputMap[self::OUTPUT_GENERATION] ?? self::OUTPUT_GENERATION, $result->generations()[0]);

        if ( ! $this->outputParser instanceof OutputParser) {
            return;
        }

        $parsed = $this->outputParser->parse($result->generations()[0]);
        foreach ($parsed as $key => $value) {
            $context->put($this->outputMap[$key] ?? $key, $value);
        }
    }
}
