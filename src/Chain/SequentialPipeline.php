<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

final class SequentialPipeline implements Pipeline
{
    /**
     * @var Chain[]
     */
    private array $chains;

    /**
     * @param Chain[] $chains
     */
    public function __construct(Chain ...$chains)
    {
        $this->chains = $chains;
    }

    public function process(Input $input): Output
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