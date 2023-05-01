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
use Vexo\Chain\Chain;
use Vexo\Chain\Input;
use Vexo\Chain\Output;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Tool\Resolver\Resolver;

final class ZeroShotAgentExecutor implements Chain, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        private readonly Agent $agent,
        private readonly Resolver $toolResolver,
        private readonly ?int $maxIterations = 15,
        private readonly ?int $maxTime = null
    ) {
    }

    public function inputKeys(): array
    {
        return ['question'];
    }

    public function outputKeys(): array
    {
        return ['result', 'intermediateSteps'];
    }

    public function process(Input $input): Output
    {
        $startTime = time();
        $timeElapsed = 0;
        $iterations = 0;
        $intermediateSteps = new Steps();

        $this->emit(new AgentExecutorStartedProcessing($input));

        while ($this->shouldContinue($timeElapsed, $iterations)) {
            $this->emit(new AgentExecutorStartedRunIteration($intermediateSteps, $iterations, $timeElapsed));

            $nextStep = $this->takeNextStep($input, $intermediateSteps);
            $intermediateSteps->add($nextStep);

            if ($nextStep->action() instanceof Finish) {
                $results = $nextStep->action()->results();
                $results['intermediateSteps'] = $intermediateSteps;

                $this->emit(new AgentExecutorFinishedProcessing($results, $iterations, $timeElapsed));

                return new Output($results);
            }

            $timeElapsed = time() - $startTime;
            $iterations++;
        }

        $this->emit(new AgentExecutorForcedStop($intermediateSteps, $iterations, $timeElapsed));

        return new Output([
            'result' => 'Failed to answer question. Max iterations or time reached',
            'intermediateSteps' => $intermediateSteps
        ]);
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

    private function takeNextStep(Input $input, Steps $intermediateSteps): Step
    {
        $nextStep = $this->agent->plan($input, $intermediateSteps);
        if ($nextStep->action() instanceof Finish) {
            return $nextStep;
        }

        $action = $nextStep->action();
        $tool = $this->toolResolver->resolve($action->tool(), $action->input());
        $observation = $tool->run($action->input());

        return $nextStep->withObservation($observation);
    }
}
