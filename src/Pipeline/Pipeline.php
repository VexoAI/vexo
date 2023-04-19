<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline;

use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;

interface Pipeline
{
    public function process(Input $input): Output;
}
