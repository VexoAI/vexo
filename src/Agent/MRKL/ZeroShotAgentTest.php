<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Action;
use Vexo\Agent\AgentFinishedPlanningNextStep;
use Vexo\Agent\AgentStartedPlanningNextStep;
use Vexo\Agent\Finish;
use Vexo\Agent\Steps;
use Vexo\Chain\Input;
use Vexo\LLM\FakeLLM;
use Vexo\LLM\Response;
use Vexo\Tool\Callback;
use Vexo\Tool\Tools;

#[CoversClass(ZeroShotAgent::class)]
#[IgnoreClassForCodeCoverage(AgentStartedPlanningNextStep::class)]
#[IgnoreClassForCodeCoverage(AgentFinishedPlanningNextStep::class)]
final class ZeroShotAgentTest extends TestCase
{
    private FakeLLM $llm;
    private Callback $toolA;
    private Callback $toolB;

    protected function setUp(): void
    {
        $this->llm = new FakeLLM([
            Response::fromString("I should do something.\nAction: ToolA\nAction Input: Some input"),
            Response::fromString('Final Answer: 42'),
        ]);

        $this->toolA = new Callback('ToolA', 'ToolA description', fn (string $input) => $input . ' - processed by ToolA');
        $this->toolB = new Callback('ToolB', 'ToolB description', fn (string $input) => $input . ' - processed by ToolB');
    }

    public function testFromLLMAndTools(): void
    {
        $eventDispatcher = new EventDispatcher();
        $zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->llm, new Tools([$this->toolA, $this->toolB]), $eventDispatcher);
        $this->assertInstanceOf(ZeroShotAgent::class, $zeroShotAgent);
        $this->assertSame($eventDispatcher, $zeroShotAgent->eventDispatcher());
    }

    public function testCreatePromptTemplate(): void
    {
        $promptTemplate = ZeroShotAgent::createPromptTemplate(new Tools([$this->toolA, $this->toolB]));
        $renderedPrompt = $promptTemplate->render(['question' => 'What is the meaning of life?', 'scratchpad' => '']);
        $renderedTemplate = (string) $renderedPrompt;

        $this->assertStringContainsString('ToolA', $renderedTemplate);
        $this->assertStringContainsString('ToolB', $renderedTemplate);
    }

    public function testPlan(): void
    {
        $agent = ZeroShotAgent::fromLLMAndTools($this->llm, new Tools([$this->toolA, $this->toolB]));
        $input = new Input(['question' => 'What is the meaning of life?']);

        $nextStep = $agent->plan($input);
        $this->assertInstanceOf(Action::class, $nextStep->action());
        $this->assertEquals('ToolA', $nextStep->action()->tool());
        $this->assertEquals('Some input', $nextStep->action()->input());

        $nextStep = $agent->plan($input, new Steps([$nextStep]));
        $this->assertInstanceOf(Finish::class, $nextStep->action());
        $this->assertEquals('42', $nextStep->action()->results()['result']);
    }
}
