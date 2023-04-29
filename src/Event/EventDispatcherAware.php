<?php

declare(strict_types=1);

namespace Vexo\Event;

use Psr\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherAware
{
    public function useEventDispatcher(EventDispatcherInterface $dispatcher): void;

    public function eventDispatcher(): EventDispatcherInterface;
}
