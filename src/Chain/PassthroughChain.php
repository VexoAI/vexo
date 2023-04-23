<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

final class PassthroughChain extends BaseChain
{
    public function __construct(
        private array $inputKeys = [],
        private array $outputKeys = []
    ) {
    }

    public function inputKeys(): array
    {
        return $this->inputKeys;
    }

    public function outputKeys(): array
    {
        return $this->outputKeys;
    }

    protected function call(Input $input): Output
    {
        return new Output($input->data());
    }
}
