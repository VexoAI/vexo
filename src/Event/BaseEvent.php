<?php

declare(strict_types=1);

namespace Vexo\Event;

abstract class BaseEvent implements SomethingHappened
{
    public string $emitter;

    public function for(object $emitter): self
    {
        $this->emitter = $emitter::class;

        return $this;
    }

    public function payload(): array
    {
        return get_object_vars($this);
    }
}
