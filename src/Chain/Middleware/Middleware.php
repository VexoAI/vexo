<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain\Middleware;

use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Output;

interface Middleware
{
    public function process(Input $input, callable $next): Output;
}