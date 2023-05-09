<?php

declare(strict_types=1);

namespace Vexo\Contract\Event;

use League\Event\EventDispatcher;
use Psr\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherAwareBehavior
{
    protected ?EventDispatcherInterface $dispatcher = null;

    public function useEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    public function eventDispatcher(): EventDispatcherInterface
    {
        if ($this->dispatcher === null) {
            $this->dispatcher = new EventDispatcher();
        }

        return $this->dispatcher;
    }

    protected function emit(SomethingHappened $event): void
    {
        $this->eventDispatcher()->dispatch($event->for($this));
    }
}
