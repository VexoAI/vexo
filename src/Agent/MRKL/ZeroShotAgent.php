<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use League\Event\EventDispatcher;
use Vexo\Agent\Agent;
use Vexo\Agent\AgentFinishedPlanningNextStep;
use Vexo\Agent\AgentOutputParser;
use Vexo\Agent\AgentStartedPlanningNextStep;
use Vexo\Agent\Step;
use Vexo\Agent\Steps;
use Vexo\Chain\Chain;
use Vexo\Chain\Input;
use Vexo\Chain\LanguageModelChain;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Model\LanguageModel;
use Vexo\Prompt\BasicPromptTemplate;
use Vexo\Tool\Tool;
use Vexo\Tool\Tools;

final class ZeroShotAgent implements Agent, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(
        private Chain $languageModelChain,
        private AgentOutputParser $outputParser,
        private string $outputKey,
        private string $languageModelPrefix = 'Thought: ',
        private string $observationPrefix = 'Observation: '
    ) {
    }

    public static function fromLLMAndTools(LanguageModel $languageModel, Tools $tools, ?EventDispatcher $eventDispatcher = null): self
    {
        $languageModelChain = new LanguageModelChain(
            languageModel: $languageModel,
            promptTemplate: self::createPromptTemplate($tools),
            inputKeys: ['question'],
            outputKey: 'text',
            stops: ['Observation:']
        );

        $agent = new self(
            languageModelChain: $languageModelChain,
            outputParser: new OutputParser(),
            outputKey: 'text'
        );

        if ($eventDispatcher !== null) {
            $languageModelChain->useEventDispatcher($eventDispatcher);
            $agent->useEventDispatcher($eventDispatcher);
        }

        return $agent;
    }

    public static function createPromptTemplate(Tools $tools): BasicPromptTemplate
    {
        // Should simply use $tools->columns('name') here, but https://github.com/ramsey/collection/issues/122
        $toolNames = implode(', ', $tools->map(fn (Tool $tool) => $tool->name())->toArray());
        $formatInstructions = str_replace('{{tool_names}}', $toolNames, Prompt::FORMAT_INSTRUCTIONS);

        $toolList = implode("\n", $tools->map(fn (Tool $tool) => $tool->name() . ': ' . $tool->description())->toArray());

        return new BasicPromptTemplate(
            implode("\n\n", [Prompt::PREFIX, $toolList, $formatInstructions, Prompt::SUFFIX]),
            ['question', 'scratchpad']
        );
    }

    public function plan(Input $input, Steps $intermediateSteps = new Steps()): Step
    {
        $this->emit(new AgentStartedPlanningNextStep($input, $intermediateSteps));

        $output = $this->languageModelChain->process(
            $this->buildFullInput($input, $intermediateSteps)
        );

        $outputText = $output->get($this->outputKey);
        $nextAction = $this->outputParser->parse($outputText);

        $step = new Step($nextAction, $outputText);

        $this->emit(new AgentFinishedPlanningNextStep($input, $intermediateSteps, $step));

        return $step;
    }

    private function buildFullInput(Input $input, Steps $intermediateSteps): Input
    {
        return new Input(
            array_merge($input->toArray(), ['scratchpad' => $this->createScratchpad($intermediateSteps)])
        );
    }

    private function createScratchpad(Steps $intermediateSteps): string
    {
        return $intermediateSteps->reduce(
            fn (string $scratchpad, Step $step) => $scratchpad . $step->log() . $this->observationPrefix . $step->observation() . "\n" . $this->languageModelPrefix,
            ''
        );
    }
}
