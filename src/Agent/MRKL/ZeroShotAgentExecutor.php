<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent\MRKL;

use Vexo\Weave\Agent\Agent;
use Vexo\Weave\Agent\Finish;
use Vexo\Weave\Agent\Step;
use Vexo\Weave\Chain\Chain;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\Logging\SupportsLogging;
use Vexo\Weave\Tool\Tool;

final class ZeroShotAgentExecutor implements Chain
{
    use SupportsLogging;

    /**
     * @param Agent $agent
     * @param Tool[] $tools
     */
    public function __construct(
        private Agent $agent,
        private array $tools,
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
        $intermediateSteps = [];

        $this->logger()->debug('Starting agent', ['input' => $input->data()]);

        while ($this->shouldContinue($timeElapsed, $iterations)) {
            $this->logger()->debug('Starting iteration', ['iteration' => $iterations]);

            $nextStep = $this->takeNextStep($input, $intermediateSteps);
            $intermediateSteps[] = $nextStep;

            if ($nextStep->action() instanceof Finish) {
                $results = $nextStep->action()->results();
                $results['intermediateSteps'] = $intermediateSteps;

                $this->logger()->debug('Agent finished', ['results' => $results]);

                return new Output($results);
            }

            $timeElapsed = time() - $startTime;
            $iterations++;

            $this->logger()->debug('Finished iteration', ['iteration' => $iterations]);
        }

        $this->logger()->debug('Agent stopped due to max iterations or time reached');

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

    private function takeNextStep(Input $input, array $intermediateSteps): Step
    {
        $nextStep = $this->agent->plan($input, $intermediateSteps);

        $this->logger()->debug('Agent took step', ['step' => $nextStep]);

        if ($nextStep->action() instanceof Finish) {
            return $nextStep;
        }

        $action = $nextStep->action();
        $tool = $this->tools[trim(strtolower($action->tool()))];
        $observation = $tool->run($action->input());

        return $nextStep->withObservation($observation);
    }
}
