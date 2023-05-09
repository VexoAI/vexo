<?php

declare(strict_types=1);

namespace Vexo\Chain\ConcatenateDocumentsChain;

use Vexo\Chain\BaseChain;
use Vexo\Chain\FailedToValidateInput;
use Vexo\Chain\Input;
use Vexo\Chain\Output;
use Vexo\Contract\Document\Documents;

final class ConcatenateDocumentsChain extends BaseChain
{
    public function __construct(
        private readonly string $inputKey = 'documents',
        private readonly string $outputKey = 'text'
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
        $documents = $input->get($this->inputKey);

        if ( ! $documents instanceof Documents) {
            throw new FailedToValidateInput('Input must be an instance of Documents');
        }

        $stuffedContents = implode(
            "\n\n",
            array_map(fn ($document): string => $document->contents(), $documents->toArray())
        );

        return new Output([$this->outputKey => $stuffedContents]);
    }
}
