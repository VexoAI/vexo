<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline\Middleware;

use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;

interface Middleware
{
    public function process(Input $input, callable $next): Output;
}
