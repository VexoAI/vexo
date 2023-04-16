<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

final class SimpleInputFactory implements InputFactory
{
    public function fromInput(Input $input): Input
    {
        return new SimpleInput($input->data());
    }

    public function fromOutput(Output $output): Input
    {
        return new SimpleInput($output->data());
    }
}