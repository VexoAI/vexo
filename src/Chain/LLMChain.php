<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Assert\Assertion as Ensure;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\LLM\LLM;
use Vexo\Weave\Prompt\Prompts;
use Vexo\Weave\Prompt\Renderer;

final class LLMChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;

    public function __construct(
        private LLM $llm,
        private Renderer $promptRenderer,
        private string $promptTemplate = '{{text}}',
        private array $inputVariables = ['text'],
        private string $outputVariable = 'text'
    ) {
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);

        $this->logger()->debug('Generating response', ['input' => $input->data()]);

        $prompts = $this->createPromptsFromInput($input);

        $response = $this->llm->generate($prompts);

        return new Output(
            [$this->outputVariable => (string) $response->generations()]
        );
    }

    private function createPromptsFromInput(Input $input): Prompts
    {
        return new Prompts(
            $this->promptRenderer->render($this->promptTemplate, $input->data())
        );
    }

    private function validateInput(Input $input): void
    {
        foreach ($this->inputVariables as $inputVariable) {
            Ensure::keyExists($input->data(), $inputVariable);
        }
    }
}
