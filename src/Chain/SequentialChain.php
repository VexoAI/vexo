<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Assert\Assertion as Ensure;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Chain\Validation\SorryValidationFailed;
use Vexo\Weave\Chain\Validation\SupportsInputValidation;
use Vexo\Weave\Logging\SupportsLogging;

final class SequentialChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;
    use SupportsInputValidation;

    /**
     * @param Chain[] $chains
     */
    public function __construct(
        private array $chains,
        private array $inputKeys = [],
        private array $outputKeys = [],
        private bool $outputAll = false
    ) {
        $this->validateChains();
        $this->validateExpectedInputsAndOutputs();
    }

    public function inputKeys(): array
    {
        return $this->inputKeys;
    }

    public function outputKeys(): array
    {
        return $this->outputKeys;
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);

        $knownValues = $input->data();

        foreach ($this->chains as $chain) {
            $this->logger()->debug('Processing chain', ['chain' => $chain::class, 'knownValues' => $knownValues]);
            $output = $chain->process(new Input($knownValues));
            $this->logger()->debug('Done processing chain', ['output' => $output->data()]);

            $knownValues = array_merge($knownValues, $output->data());
        }

        return new Output(array_intersect_key($knownValues, array_flip($this->outputKeys)));
    }

    private function validateChains(): void
    {
        $this->try(
            function () {
                Ensure::allIsInstanceOf($this->chains, Chain::class);
                Ensure::minCount($this->chains, 1, 'At least one chain is required');
            }
        );
    }

    private function validateExpectedInputsAndOutputs(): void
    {
        $availableVariables = $this->inputKeys;

        // Go through each chain and validate that any input is known at the point in the sequence where the chain will
        // be called. Known variables are the input variables of the chain and the output variables of all previous
        // chains. Also validate that the output variables of each chain do not override known variables.
        foreach ($this->chains as $chain) {
            $this->validateMissingInputVariables($chain, $availableVariables);
            $this->validateOverlappingOutputVariables($chain, $availableVariables);

            // Add the output variables of this chain to the available variables
            $availableVariables = array_merge($availableVariables, $chain->outputKeys());
        }

        $this->determineOutputKeys($availableVariables);
    }

    private function validateMissingInputVariables(Chain $chain, array $availableVariables): void
    {
        $missingVariables = array_diff_key(array_flip($chain->inputKeys()), array_flip($availableVariables));
        if ( ! empty($missingVariables)) {
            throw new SorryValidationFailed(
                sprintf(
                    'Chain %s has input variables that are not known: %s, only had %s',
                    $chain::class,
                    implode(', ', array_keys($missingVariables)),
                    implode(', ', $availableVariables)
                )
            );
        }
    }

    private function validateOverlappingOutputVariables(Chain $chain, array $availableVariables): void
    {
        $overlappingVariables = array_intersect_key(array_flip($chain->outputKeys()), array_flip($availableVariables));
        if ( ! empty($overlappingVariables)) {
            throw new SorryValidationFailed(
                sprintf(
                    'Chain %s has output variables that would override known variables: %s',
                    $chain::class,
                    implode(', ', array_keys($overlappingVariables))
                )
            );
        }
    }

    private function determineOutputKeys(array $availableVariables): void
    {
        // If we are supposed to output all available variables, use them as the final output variables
        if ($this->outputAll) {
            $this->outputKeys = $availableVariables;

            return;
        }

        // If no final output variables are specified, use the output variables of the last chain
        if (empty($this->outputKeys)) {
            $this->outputKeys = end($this->chains)->outputKeys();

            return;
        }

        // Check if our final output variables are known at the end of the sequence
        $missingVariables = array_diff_key(array_flip($this->outputKeys), array_flip($availableVariables));
        if ( ! empty($missingVariables)) {
            throw new SorryValidationFailed(
                sprintf(
                    'Output variables are not produced by this sequence: %s',
                    implode(', ', array_keys($missingVariables))
                )
            );
        }
    }
}
