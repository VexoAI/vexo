<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Chain\Validation\SupportsInputValidation;
use Vexo\Weave\LLM\LLM;
use Vexo\Weave\Logging\SupportsLogging;
use Vexo\Weave\Prompt\Renderer;

final class LLMChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;
    use SupportsInputValidation;

    public function __construct(
        private LLM $llm,
        private Renderer $promptRenderer,
        private string $promptTemplate = '{{text}}',
        private array $inputKeys = ['text'],
        private string $outputKey = 'text'
    ) {
    }

    public function inputKeys(): array
    {
        return $this->inputKeys;
    }

    public function outputKeys(): array
    {
        return [$this->outputKey];
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);

        $this->logger()->debug('Generating response', ['input' => $input->data()]);

        $prompt = $this->promptRenderer->render($this->promptTemplate, $input->data());

        $response = $this->llm->generate($prompt);

        return new Output(
            [$this->outputKey => (string) $response->generations()]
        );
    }
}
