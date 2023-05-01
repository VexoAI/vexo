<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Model\LanguageModel;
use Vexo\Prompt\PromptTemplate;

final class LanguageModelChain extends BaseChain
{
    public function __construct(
        private readonly LanguageModel $languageModel,
        private readonly PromptTemplate $promptTemplate,
        private readonly array $inputKeys = ['text'],
        private readonly string $outputKey = 'text',
        private readonly array $stops = []
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
        $response = $this->languageModel->generate(
            $this->promptTemplate->render($input->toArray()),
            ...$this->stops
        );

        return new Output(
            [$this->outputKey => (string) $response->completions()]
        );
    }
}
