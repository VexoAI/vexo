<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use League\Event\EventDispatcher;
use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
use Vexo\Agent\Action;
use Vexo\Agent\Agent;
use Vexo\Agent\AgentFinishedPlanningNextStep;
use Vexo\Agent\AgentStartedPlanningNextStep;
use Vexo\Agent\Finish;
use Vexo\Agent\Step;
use Vexo\Chain\Chain;
use Vexo\Chain\Input;
use Vexo\Chain\LLMChain;
use Vexo\LLM\LLM;
use Vexo\Prompt\BasicPromptTemplate;
use Vexo\Tool\Tool;

final class ZeroShotAgent implements Agent, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        private Chain $llmChain,
        private string $outputKey,
        private string $llmPrefix = 'Thought: ',
        private string $observationPrefix = 'Observation: '
    ) {
    }

    /**
     * @param LLM $llm
     * @param Tool[] $tools
     * @param EventDispatcher $eventDispatcher
     */
    public static function fromLLMAndTools(LLM $llm, array $tools, ?EventDispatcher $eventDispatcher = null): ZeroShotAgent
    {
        $llmChain = new LLMChain(
            llm: $llm,
            promptTemplate: ZeroShotAgent::createPromptTemplate($tools),
            inputKeys: ['question'],
            outputKey: 'text',
            stops: ['Observation:']
        );

        $agent = new ZeroShotAgent(
            llmChain: $llmChain,
            outputKey: 'text'
        );

        if ($eventDispatcher !== null) {
            $llmChain->useEventDispatcher($eventDispatcher);
            $agent->useEventDispatcher($eventDispatcher);
        }

        return $agent;
    }

    /**
     * @param Tool[] $tools
     */
    public static function createPromptTemplate(array $tools): BasicPromptTemplate
    {
        $toolNames = implode(', ', array_map(fn (Tool $tool) => $tool->name(), $tools));
        $formatInstructions = str_replace('{{tool_names}}', $toolNames, Prompt::FORMAT_INSTRUCTIONS);

        $toolList = implode("\n", array_map(fn (Tool $tool) => $tool->name() . ': ' . $tool->description(), $tools));

        return new BasicPromptTemplate(
            implode("\n\n", [Prompt::PREFIX, $toolList, $formatInstructions, Prompt::SUFFIX]),
            ['question', 'scratchpad']
        );
    }

    /**
     * @param Step[] $intermediateSteps
     */
    public function plan(Input $input, array $intermediateSteps = []): Step
    {
        $this->eventDispatcher()->dispatch(
            (new AgentStartedPlanningNextStep($input, $intermediateSteps))->for($this)
        );

        $output = $this->llmChain->process(
            $this->buildFullInput($input, $intermediateSteps)
        );

        $outputText = $output->get($this->outputKey);
        $nextAction = $this->parseOutput($outputText);

        $step = new Step($nextAction, $outputText);

        $this->eventDispatcher()->dispatch(
            (new AgentFinishedPlanningNextStep($input, $intermediateSteps, $step))->for($this)
        );

        return $step;
    }

    private function buildFullInput(Input $input, array $intermediateSteps): Input
    {
        return new Input(
            array_merge($input->data(), ['scratchpad' => $this->createScratchpad($intermediateSteps)])
        );
    }

    private function createScratchpad(array $intermediateSteps): string
    {
        $scratchpad = '';
        foreach ($intermediateSteps as $step) {
            $scratchpad .= $step->log();
            $scratchpad .= $this->observationPrefix . $step->observation() . "\n" . $this->llmPrefix;
        }

        return $scratchpad;
    }

    private function parseOutput(string $output): Action|Finish
    {
        if (preg_match('/Final Answer:/', $output)) {
            $answer = explode('Final Answer:', $output);

            return new Finish(['result' => trim(end($answer))]);
        }

        $matches = [];
        if (preg_match('/Action:\s*(.*?)\nAction\s*Input:\s*(.*)/', $output, $matches)) {
            return new Action($matches[1], trim($matches[2]));
        }

        throw new \RuntimeException('Could not parse output');
    }
}
