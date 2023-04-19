<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline\Concerns;

use Psr\Log\LoggerInterface;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\Pipeline\Middleware\Middleware;

trait SupportsMiddleware
{
    /**
     * @var Middleware[]
     */
    private array $middlewares = [];

    /**
     * @param Middleware $middleware The middleware to be added
     */
    public function addMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return LoggerInterface
     */
    abstract private function logger(): LoggerInterface;

    /**
     * @param Input $input The input to be processed
     * @param callable $corePipeline The core pipeline function to be executed
     */
    private function processWithMiddlewares(Input $input, callable $corePipeline): Output
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
        $logger = $this->logger();
        $middlewareRunner = $corePipeline;

        while ($middleware = array_pop($middlewares)) {
            $middlewareRunner = function (Input $input) use ($middleware, $middlewareRunner, $logger) {
                $logger->debug('Executing middleware', ['middleware' => get_class($middleware)]);
                $newRunner = $middleware->process($input, $middlewareRunner);
                $logger->debug('End middleware execution', ['middleware' => get_class($middleware)]);

                return $newRunner;
            };
        }

        return $middlewareRunner;
    }
}
