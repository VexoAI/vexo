<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

interface Chain
{
    public function process(Input $input): Output;
}
