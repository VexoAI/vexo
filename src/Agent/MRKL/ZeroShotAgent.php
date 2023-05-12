<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Agent\Agent;
use Vexo\Agent\AgentFinishedPlanningNextStep;
use Vexo\Agent\AgentOutputParser;
use Vexo\Agent\AgentStartedPlanningNextStep;
use Vexo\Agent\Step;
use Vexo\Agent\Steps;
use Vexo\Agent\Tool\Tool;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\LanguageModelChain;
use Vexo\Contract\Event\Event;
use Vexo\LanguageModel\LanguageModel;
use Vexo\LanguageModel\Prompt\BasicPromptTemplate;

final class ZeroShotAgent implements Agent
{
    public function __construct(
        private readonly Chain $languageModelChain,
        private readonly AgentOutputParser $outputParser,
        private readonly string $languageModelPrefix = 'Thought: ',
        private readonly string $observationPrefix = 'Observation: ',
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
    }

    public static function fromLLMAndTools(
        LanguageModel $languageModel,
        Tools $tools,
        ?EventDispatcherInterface $eventDispatcher = null
    ): self {
        $languageModelChain = new LanguageModelChain(
            languageModel: $languageModel,
            promptTemplate: self::createPromptTemplate($tools),
            stops: ['Observation:']
        );

        return new self(
            languageModelChain: $languageModelChain,
            outputParser: new OutputParser(),
            eventDispatcher: $eventDispatcher
        );
    }

    public static function createPromptTemplate(Tools $tools): BasicPromptTemplate
    {
        // Should simply use $tools->columns('name') here, but https://github.com/ramsey/collection/issues/122
        $toolNames = implode(', ', $tools->map(fn (Tool $tool): string => $tool->name())->toArray());
        $formatInstructions = str_replace('{{tool_names}}', $toolNames, Prompt::FORMAT_INSTRUCTIONS);

        $toolList = implode("\n", $tools->map(fn (Tool $tool): string => $tool->name() . ': ' . $tool->description())->toArray());

        return new BasicPromptTemplate(
            implode("\n\n", [Prompt::PREFIX, $toolList, $formatInstructions, Prompt::SUFFIX]),
            ['question', 'scratchpad']
        );
    }

    public function plan(Context $context, Steps $intermediateSteps = new Steps()): Step
    {
        $this->emit(new AgentStartedPlanningNextStep($context, $intermediateSteps));

        $context->put('scratchpad', $this->createScratchpad($intermediateSteps));

        $this->languageModelChain->run($context);

        $outputText = $context->get('text');
        $nextAction = $this->outputParser->parse($outputText);

        $step = new Step($nextAction, $outputText);

        $this->emit(new AgentFinishedPlanningNextStep($context, $intermediateSteps, $step));

        return $step;
    }

    private function createScratchpad(Steps $intermediateSteps): string
    {
        return $intermediateSteps->reduce(
            fn (string $scratchpad, Step $step): string => $scratchpad . $step->log() . $this->observationPrefix . $step->observation() . "\n" . $this->languageModelPrefix,
            ''
        );
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
