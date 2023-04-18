<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain\Concerns;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Vexo\Weave\Chain\Input;

trait SupportsValidation
{
    private function validateInput(Input $input): void
    {
        $validator = Validation::createValidator();
        $constraints = $this->inputConstraints();

        $violations = $validator->validate($input->data(), $constraints);
        if (count($violations) > 0) {
            $violationMessages = [];
            foreach ($violations as $violation) {
                $violationMessages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            throw new \InvalidArgumentException(implode("\n", $violationMessages));
        }
    }

    protected abstract function inputConstraints(): Constraint;
}