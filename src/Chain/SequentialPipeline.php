<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

use Pragmatist\Assistant\Chain\Middleware\Middleware;
use Pragmatist\Assistant\Chain\Middleware\MiddlewarePlumbing;

final class SequentialPipeline implements Pipeline
{
    use MiddlewarePlumbing;

    /**
     * @var Chain[]
     */
    private array $chains;

    /**
     * @param Chain[] $chains
     * @param Middleware[] $middlewares
     */
    public function __construct(array $chains, array $middlewares = [])
    {
        $this->chains = $chains;
        $this->middlewares = $middlewares;
    }

    public function process(Input $input): Output
    {
        return $this->processWithMiddlewares($input, function (Input $input) {
            return $this->processChains($input);
        });
    }

    private function processChains(Input $input): Output
    {
        $output = null;

        foreach ($this->chains as $chain) {
            if ($output === null) {
                $output = $chain->process($input);
                continue;
            }

            $output = $chain->process(
                $chain->inputFactory()->fromOutput($output)
            );
        }

        return $output;
    }
}