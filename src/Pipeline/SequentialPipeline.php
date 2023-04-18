<?php

declare(strict_types=1);

namespace Vexo\Weave\Pipeline;

use Assert\Assertion as Ensure;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Chain\Chain;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\Pipeline\Concerns\SupportsMiddleware;

final class SequentialPipeline implements MiddlewarePipeline, LoggerAwareInterface
{
    use SupportsLogging;
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
