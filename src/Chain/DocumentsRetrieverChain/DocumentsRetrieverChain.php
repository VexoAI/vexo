<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use Vexo\Chain\BaseChain;
use Vexo\Chain\Input;
use Vexo\Chain\Output;
use Vexo\Retriever\Retriever;

final class DocumentsRetrieverChain extends BaseChain
{
    public function __construct(
        private readonly Retriever $retriever,
        private readonly string $inputKey = 'query',
        private readonly string $outputKey = 'documents'
    ) {
    }

    public function inputKeys(): array
    {
        return [$this->inputKey];
    }

    public function outputKeys(): array
    {
        return [$this->outputKey];
    }

    protected function call(Input $input): Output
    {
        return new Output([
            $this->outputKey => $this->retriever->retrieve(
                $input->get($this->inputKey)
            )
        ]);
    }
}
