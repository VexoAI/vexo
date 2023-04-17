<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Pipeline;

use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Output;

interface Pipeline
{
    public function process(Input $input): Output;
}