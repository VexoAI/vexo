<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

interface InputFactory
{
    public function fromInput(Input $input): Input;

    public function fromOutput(Output $output): Input;
}