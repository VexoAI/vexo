<?php

declare(strict_types=1);

namespace Vexo\Chain\SequentialChain;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Chain\Chain;
use Vexo\Chain\ChainFinished;
use Vexo\Chain\ChainStarted;
use Vexo\Chain\Context;
use Vexo\Contract\Event;

final class SequentialChain implements Chain
{
    /**
     * @var array<Chain>
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
        $this->chains[] = $chain;

        return $this;
    }

    public function run(Context $context): void
    {
        foreach ($this->chains as $identifier => $chain) {
            $identifier = spl_object_hash($chain);
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
