<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain\Middleware;

use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Output;

final class PassthroughMiddleware implements Middleware
{
    public function process(Input $input, callable $next): Output
    {
        return $next($input);
    }
}