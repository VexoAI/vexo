<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Pipeline;

use Pragmatist\Assistant\Pipeline\Middleware\Middleware;

interface MiddlewarePipeline extends Pipeline
{
    public function addMiddleware(Middleware $middleware): void;
}