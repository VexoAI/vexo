<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Assert\Assertion as Ensure;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;

abstract class BaseChain implements Chain, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    abstract public function inputKeys(): array;

    abstract public function outputKeys(): array;

    public function process(Input $input): Output
    {
        $this->emit(new ChainStarted($input));

        $this->validateInput($input);
        $output = $this->call($input);

        $this->emit(new ChainFinished($input, $output));

        return $output;
    }

    abstract protected function call(Input $input): Output;

    protected function validateInput(Input $input): void
    {
        foreach ($this->inputKeys() as $inputKey) {
            $this->try(
                function () use ($input, $inputKey): void {
                    Ensure::keyExists(
                        $input->data(),
                        $inputKey,
                        sprintf(
                            'Input data is missing required key "%s". Recieved: %s',
                            $inputKey,
                            implode(', ', array_keys($input->data()))
                        )
                    );
                }
            );
        }
    }

    protected function try(callable $callable): void
    {
        try {
            $callable();
        } catch (\Throwable $e) {
            throw new SorryValidationFailed($e->getMessage(), $e->getCode(), $e);
        }
    }
}
