<?php

declare(strict_types=1);

namespace Vexo\Event;

interface SomethingHappened
{
    public function for(object $emitter): self;

    public function payload(): array;
}
