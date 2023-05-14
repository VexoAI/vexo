<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Contract\Event\Event;

final class ModelGeneratedResult implements Event
{
    /**
     * @param array<string> $stops
     */
    public function __construct(
        private readonly string $prompt,
        private readonly array $stops,
        private readonly Result $result
    ) {
    }

    public function prompt(): string
    {
        return $this->prompt;
    }

    public function stops(): array
    {
        return $this->stops;
    }

    public function result(): Result
    {
        return $this->result;
    }
}
