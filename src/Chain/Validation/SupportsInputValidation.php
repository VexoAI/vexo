<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain\Validation;

use Assert\Assertion as Ensure;
use Vexo\Weave\Chain\Input;

trait SupportsInputValidation
{
    private function validateInput(Input $input): void
    {
        foreach ($this->inputKeys() as $inputKey) {
            $this->try(
                function () use ($input, $inputKey) {
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

    private function try(callable $callable): void
    {
        try {
            $callable();
        } catch (\Throwable $e) {
            throw new SorryValidationFailed($e->getMessage(), $e->getCode(), $e);
        }
    }
}
