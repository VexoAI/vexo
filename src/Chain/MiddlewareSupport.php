<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Middleware\Middleware;
use Pragmatist\Assistant\Chain\Output;

trait MiddlewareSupport
{
    /**
     * @var Middleware[]
     */
    private array $middlewares;

    /**
     * @param Middleware $middleware The middleware to be added
     */
    public function addMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @param Input $input The input to be processed
     * @param callable $corePipeline The core pipeline function to be executed
     */
    protected function processWithMiddlewares(Input $input, callable $corePipeline): Output
    {
        $middlewareCallable = $this->createMiddlewareCallable($this->middlewares, $corePipeline);
        return $middlewareCallable($input);
    }

    /**
     * @param Middleware[] $middlewares The middlewares to be executed
     * @param callable $corePipeline The core pipeline function to be executed
     */
    private function createMiddlewareCallable(array $middlewares, callable $corePipeline): callable
    {
        $middlewareRunner = $corePipeline;

        while ($middleware = array_pop($middlewares)) {
            $middlewareRunner = function (Input $input) use ($middleware, $middlewareRunner) {
                return $middleware->process($input, $middlewareRunner);
            };
        }

        return $middlewareRunner;
    }
}
