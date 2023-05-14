<?php

declare(strict_types=1);

namespace Vexo\Agent;

final class Conclusion
{
    public function __construct(
        private readonly string $thought,
        private readonly string $observation
    ) {
    }

    public function thought(): string
    {
        return $this->thought;
    }

    public function observation(): ?string
    {
        return $this->observation;
    }
}
