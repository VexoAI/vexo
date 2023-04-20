<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Vexo\Weave\Chain\Validation\SupportsInputValidation;

final class PassthroughChain implements Chain
{
    use SupportsInputValidation;

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

    public function process(Input $input): Output
    {
        $this->validateInput($input);

        return new Output($input->data());
    }
}
