<?php

declare(strict_types=1);

namespace Vexo\Prompt;

final class BasicPromptTemplate implements PromptTemplate
{
    public function __construct(
        private readonly string $template,
        private readonly array $variables
    ) {
    }

    public function render(array $values): Prompt
    {
        $this->validateValues($values);

        return new Prompt(
            str_replace(
                array_map(fn ($variable) => "{{{$variable}}}", $this->variables),
                array_intersect_key($values, array_flip($this->variables)),
                $this->template
            )
        );
    }

    private function validateValues(array $values): void
    {
        $missingValues = array_diff($this->variables, array_keys($values));
        if ($missingValues !== []) {
            throw SorryNotAllRequiredValuesWereGiven::with($missingValues);
        }
    }
}
