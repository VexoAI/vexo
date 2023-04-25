<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Step
{
    public function __construct(
        private Action|Finish $action,
        private string $log,
        private ?string $observation = null
    ) {
    }

    public function withObservation(string $observation): self
    {
        return new self(
            $this->action(),
            $this->log(),
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
