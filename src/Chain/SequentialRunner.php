<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Contract\Event\Event;

final class SequentialRunner implements Runner
{
    /**
     * @var array<string, Chain>
     */
    private array $chains = [];

    /**
     * @param array<Chain> $chains
     */
    public function __construct(
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        array $chains = []
    ) {
        foreach ($chains as $chain) {
            $this->add($chain);
        }
    }

    public function add(Chain $chain): self
    {
        $this->chains[spl_object_hash($chain)] = $chain;

        return $this;
    }

    public function run(Context $context): void
    {
        foreach ($this->chains as $identifier => $chain) {
            $this->emit(new ChainStarted($identifier, $chain::class, $context));
            $chain->run($context);
            $this->emit(new ChainFinished($identifier, $chain::class, $context));
        }
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
