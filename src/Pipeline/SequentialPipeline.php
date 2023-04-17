<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Pipeline;

use Assert\Assertion as Ensure;
use Pragmatist\Assistant\Chain\Chain;
use Pragmatist\Assistant\Chain\Input;
use Pragmatist\Assistant\Chain\Output;
use Pragmatist\Assistant\Pipeline\Concerns\SupportsMiddleware;

final class SequentialPipeline implements MiddlewarePipeline
{
    use SupportsMiddleware;

    /**
     * @param Chain[] $chains
     */
    public function __construct(private array $chains)
    {
        Ensure::allIsInstanceOf($chains, Chain::class);
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
