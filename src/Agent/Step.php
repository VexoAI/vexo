<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Step
{
    public function __construct(
        private readonly string $thought,
        private readonly string $action,
        private readonly string $input = '',
        private readonly ?string $observation = null
    ) {
    }

    public function withObservation(string $observation): self
    {
        return new self(
            $this->thought,
            $this->action,
            $this->input,
            $observation
        );
    }

    public function thought(): string
    {
        return $this->thought;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function input(): string
    {
        return $this->input;
    }

    public function observation(): ?string
    {
        return $this->observation;
    }
}
