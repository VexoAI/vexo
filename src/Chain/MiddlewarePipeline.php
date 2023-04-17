<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

use Pragmatist\Assistant\Chain\Middleware\Middleware;

interface MiddlewarePipeline extends Pipeline
{
    public function addMiddleware(Middleware $middleware): void;
}