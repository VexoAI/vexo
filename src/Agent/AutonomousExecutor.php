<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Chain\Context;
use Vexo\Contract\Event;

final class AutonomousExecutor implements Executor
{
    private int $startTime = 0;

    private int $iterationsCompleted = 0;

    public function __construct(
        private readonly Agent $agent,
        private readonly ?int $maxIterations = 15,
        private readonly ?int $maxTime = null,
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
    }

    public function run(Context $context): void
    {
        $this->startTime = time();
        $previousSteps = new Steps();
        $context->put('steps_taken', $previousSteps);

        do {
            $nextStep = $this->agent->planNextStep($context, $previousSteps);
            if ($nextStep instanceof Conclusion) {
                $context->put('conclusion', $nextStep);
                $this->emit(new ExecutorCompletedExecution($context, $previousSteps));

                return;
            }

            $completedStep = $this->agent->takeStep($context, $previousSteps, $nextStep);
            $previousSteps->add($completedStep);

            $this->emit(new ExecutorCompletedIteration($context, $previousSteps));

            $this->iterationsCompleted++;
        } while ($this->shouldContinue());

        $context->put(
            'conclusion',
            new Conclusion(
                thought: 'Max iterations or time reached. Aborting.',
                observation: 'Could not complete all steps to reach a conclusion.'
            )
        );
        $this->emit(new ExecutorAbortedAgent($context, $previousSteps));
    }

    private function shouldContinue(): bool
    {
        return ! $this->hasReachedMaxIterations()
            && ! $this->hasReachedMaxTime();
    }

    private function hasReachedMaxIterations(): bool
    {
        return $this->maxIterations !== null && $this->iterationsCompleted >= $this->maxIterations;
    }

    private function hasReachedMaxTime(): bool
    {
        return $this->maxTime !== null && (time() - $this->startTime) >= $this->maxTime;
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
