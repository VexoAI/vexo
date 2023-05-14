<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Agent\Tool\FailedToResolveTool;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class ReasonAndActAgent implements Agent
{
    public function __construct(
        private readonly Chain $languageModelChain,
        private readonly Tools $tools,
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
    }

    public function planNextStep(Context $context, Steps $previousSteps): Step|Conclusion
    {
        $scopedContext = clone $context;
        $scopedContext->put('steps', $previousSteps);
        $scopedContext->put('tools', $this->tools);

        $this->languageModelChain->run($scopedContext);

        if ($scopedContext->containsKey('final_answer')) {
            $conclusion = new Conclusion(
                thought: $scopedContext->get('final_thought', ''), // @phpstan-ignore-line
                observation: $scopedContext->get('final_answer', '') // @phpstan-ignore-line
            );

            $this->emit(new AgentReachedConclusion($context, $previousSteps, $conclusion));

            return $conclusion;
        }

        $nextStep = new Step(
            thought: $scopedContext->get('thought', ''), // @phpstan-ignore-line
            action: $scopedContext->get('action', ''), // @phpstan-ignore-line
            input: $scopedContext->get('input', '') // @phpstan-ignore-line
        );

        $this->emit(new AgentPlannedNextStep($context, $previousSteps, $nextStep));

        return $nextStep;
    }

    public function takeStep(Context $context, Steps $previousSteps, Step $step): Step
    {
        try {
            $tool = $this->tools->resolve($step->action());
            $observation = $tool->run($step->input());
        } catch (FailedToResolveTool $exception) {
            $observation = $exception->getMessage();
        } catch (\Throwable $exception) {
            $observation = sprintf('Failed to execute tool: %s', $exception->getMessage());
        }

        $completedStep = $step->withObservation($observation);
        $this->emit(new AgentTookStep($context, $previousSteps, $completedStep));

        return $completedStep;
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
