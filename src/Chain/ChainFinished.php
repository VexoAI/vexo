<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Vexo\Contract\Event\Event;

final class ChainFinished implements Event
{
    public function __construct(
        private readonly string $chainIdentifier,
        private readonly string $chainClass,
        private readonly Context $context
    ) {
    }

    public function chainIdentifier(): string
    {
        return $this->chainIdentifier;
    }

    public function chainClass(): string
    {
        return $this->chainClass;
    }

    public function context(): Context
    {
        return $this->context;
    }
}
