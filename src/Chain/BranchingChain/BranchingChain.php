<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Vexo\Chain\Chain;
use Vexo\Chain\ChainFinished;
use Vexo\Chain\ChainStarted;
use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class BranchingChain implements Chain
{
    /**
     * @var array<string, Chain>
     */
    private array $chains = [];

    /**
     * @param array<string, Chain> $chains
     */
    public function __construct(
        private readonly ExpressionLanguage $evaluator = new ExpressionLanguage(),
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        array $chains = []
    ) {
        foreach ($chains as $condition => $chain) {
            $this->add($condition, $chain);
        }
    }

    public function add(string $condition, Chain $chain): self
    {
        $this->chains[$condition] = $chain;

        return $this;
    }

    public function run(Context $context): void
    {
        foreach ($this->chains as $condition => $chain) {
            $shouldExecute = $this->evaluateCondition($condition, $context);

            $identifier = spl_object_hash($chain);
            $this->emit(
                new ChainBranchConditionEvaluated($identifier, $chain::class, $context, $condition, $shouldExecute)
            );

            if ( ! $shouldExecute) {
                continue;
            }

            $this->emit(new ChainStarted($identifier, $chain::class, $context));
            $chain->run($context);
            $this->emit(new ChainFinished($identifier, $chain::class, $context));
        }
    }

    private function evaluateCondition(string $condition, Context $context): bool
    {
        try {
            return (bool) $this->evaluator->evaluate($condition, $context->toArray());
        } catch (\Throwable $exception) {
            throw FailedToEvaluateCondition::because($exception);
        }
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
