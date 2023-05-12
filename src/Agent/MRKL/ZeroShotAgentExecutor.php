<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use Vexo\Agent\Agent;
use Vexo\Agent\AgentExecutorFinishedProcessing;
use Vexo\Agent\AgentExecutorForcedStop;
use Vexo\Agent\AgentExecutorStartedProcessing;
use Vexo\Agent\AgentExecutorStartedRunIteration;
use Vexo\Agent\Finish;
use Vexo\Agent\Step;
use Vexo\Agent\Steps;
use Vexo\Agent\Tool\Resolver\Resolver;
use Vexo\Chain\Context;
use Vexo\Contract\Event\EventDispatcherAware;
use Vexo\Contract\Event\EventDispatcherAwareBehavior;

final class ZeroShotAgentExecutor implements EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        private readonly Agent $agent,
        private readonly Resolver $toolResolver,
        private readonly ?int $maxIterations = 15,
        private readonly ?int $maxTime = null
    ) {
    }

    public function run(Context $context): void
    {
        $startTime = time();
        $timeElapsed = 0;
        $iterations = 0;
        $intermediateSteps = new Steps();

        $this->emit(new AgentExecutorStartedProcessing($context));

        while ($this->shouldContinue($timeElapsed, $iterations)) {
            $this->emit(new AgentExecutorStartedRunIteration($intermediateSteps, $iterations, $timeElapsed));

            $nextStep = $this->takeNextStep($context, $intermediateSteps);
            $intermediateSteps->add($nextStep);

            if ($nextStep->action() instanceof Finish) {
                $results = $nextStep->action()->results();

                $context->put('intermediateSteps', $intermediateSteps);
                $context->put('results', $results);

                $this->emit(new AgentExecutorFinishedProcessing($context, $iterations, $timeElapsed));

                return;
            }

            $timeElapsed = time() - $startTime;
            $iterations++;
        }

        $this->emit(new AgentExecutorForcedStop($intermediateSteps, $iterations, $timeElapsed));

        $context->put('intermediateSteps', $intermediateSteps);
        $context->put('result', 'Failed to answer question. Max iterations or time reached');
    }

    private function shouldContinue(int $timeElapsed, int $iterations): bool
    {
        return $this->isWithinMaxTime($timeElapsed)
            && $this->isWithinMaxIterations($iterations);
    }

    private function isWithinMaxTime(int $timeElapsed): bool
    {
        return $this->maxTime === null || $timeElapsed < $this->maxTime;
    }

    private function isWithinMaxIterations(int $iterations): bool
    {
        return $this->maxIterations === null || $iterations < $this->maxIterations;
    }

    private function takeNextStep(Context $context, Steps $intermediateSteps): Step
    {
        $nextStep = $this->agent->plan($context, $intermediateSteps);
        if ($nextStep->action() instanceof Finish) {
            return $nextStep;
        }

        $action = $nextStep->action();
        $tool = $this->toolResolver->resolve($action->tool(), $action->input());
        $observation = $tool->run($action->input());

        return $nextStep->withObservation($observation);
    }
}
