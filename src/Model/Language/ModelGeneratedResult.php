<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

use Vexo\Contract\Event;

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

    /**
     * @return array<string>
     */
    public function stops(): array
    {
        return $this->stops;
    }

    public function result(): Result
    {
        return $this->result;
    }
}
