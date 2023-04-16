<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

interface Pipeline
{
    public function process(Input $input): Output;
}