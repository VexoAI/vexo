<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
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
use Vexo\Tool\Resolver\Resolver;

final class ZeroShotAgentExecutor implements Chain, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        private Agent $agent,
        private Resolver $toolResolver,
        private ?int $maxIterations = 15,
        private ?int $maxTime = null
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

        $this->eventDispatcher()->dispatch(
            (new AgentExecutorStartedProcessing($input))->for($this)
        );

        while ($this->shouldContinue($timeElapsed, $iterations)) {
            $this->eventDispatcher()->dispatch(
                (new AgentExecutorStartedRunIteration($intermediateSteps, $iterations, $timeElapsed))->for($this)
            );

            $nextStep = $this->takeNextStep($input, $intermediateSteps);
            $intermediateSteps->add($nextStep);

            if ($nextStep->action() instanceof Finish) {
                $results = $nextStep->action()->results();
                $results['intermediateSteps'] = $intermediateSteps;

                $this->eventDispatcher()->dispatch(
                    (new AgentExecutorFinishedProcessing($results, $iterations, $timeElapsed))->for($this)
                );

                return new Output($results);
            }

            $timeElapsed = time() - $startTime;
            $iterations++;
        }

        $this->eventDispatcher()->dispatch(
            (new AgentExecutorForcedStop($intermediateSteps, $iterations, $timeElapsed))->for($this)
        );

        return new Output([
            'result' => 'Failed to answer question. Max iterations or time reached',
            'intermediateSteps' => $intermediateSteps
        ]);
    }

    private function shouldContinue(int $timeElapsed, int $iterations): bool
    {
        if ($this->maxTime !== null && $timeElapsed >= $this->maxTime) {
            return false;
        }

        if ($this->maxIterations !== null && $iterations >= $this->maxIterations) {
            return false;
        }

        return true;
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
