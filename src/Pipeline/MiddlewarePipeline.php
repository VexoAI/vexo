<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline;

use Vexo\Weave\Pipeline\Middleware\Middleware;

interface MiddlewarePipeline extends Pipeline
{
    public function addMiddleware(Middleware $middleware): void;
}
