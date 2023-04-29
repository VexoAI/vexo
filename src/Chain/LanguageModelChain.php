<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Model\LanguageModel;
use Vexo\Prompt\PromptTemplate;

final class LanguageModelChain extends BaseChain
{
    public function __construct(
        private LanguageModel $llm,
        private PromptTemplate $promptTemplate,
        private array $inputKeys = ['text'],
        private string $outputKey = 'text',
        private array $stops = []
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

    protected function call(Input $input): Output
    {
        $response = $this->llm->generate(
            $this->promptTemplate->render($input->data()),
            ...$this->stops
        );

        return new Output(
            [$this->outputKey => (string) $response->completions()]
        );
    }
}