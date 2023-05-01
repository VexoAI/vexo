<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Step
{
    public function __construct(
        private readonly Action|Finish $action,
        private readonly string $log,
        private readonly ?string $observation = null
    ) {
    }

    public function withObservation(string $observation): self
    {
        return new self(
            $this->action,
            $this->log,
            $observation
        );
    }

    public function action(): Action|Finish
    {
        return $this->action;
    }

    public function log(): string
    {
        return $this->log;
    }

    public function observation(): ?string
    {
        return $this->observation;
    }
}
