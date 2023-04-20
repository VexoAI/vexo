<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Assert\Assertion as Ensure;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Concerns\SupportsLogging;

final class SequentialChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;

    /**
     * @var Chain[]
     */
    private array $chains;

    public function __construct(Chain ...$chains)
    {
        Ensure::minCount($chains, 1, 'At least one chain is required');

        $this->chains = $chains;
    }

    public function process(Input $input): Output
    {
        $nextChainInput = $input;
        $output = null;

        foreach ($this->chains as $index => $chain) {
            $this->logger()->debug('Processing chain', ['input' => $nextChainInput, 'chain' => $chain::class, 'index' => $index]);
            $output = $chain->process($nextChainInput);
            $this->logger()->debug('Done processing chain', ['output' => $output]);

            $nextChainInput = new Input($output->data());
        }

        return $output;
    }
}
