<?php

declare(strict_types=1);

namespace Vexo\Chain\PassthroughChain;

use Vexo\Chain\BaseChain;
use Vexo\Chain\Input;
use Vexo\Chain\Output;

final class PassthroughChain extends BaseChain
{
    public function __construct(
        private readonly array $inputKeys = [],
        private readonly array $outputKeys = []
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
        return new Output($input->toArray());
    }
}
