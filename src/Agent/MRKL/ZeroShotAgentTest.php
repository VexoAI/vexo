<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Action;
use Vexo\Agent\AgentFinishedPlanningNextStep;
use Vexo\Agent\AgentStartedPlanningNextStep;
use Vexo\Agent\Finish;
use Vexo\Agent\Steps;
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Context;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;

#[CoversClass(ZeroShotAgent::class)]
#[IgnoreClassForCodeCoverage(AgentStartedPlanningNextStep::class)]
#[IgnoreClassForCodeCoverage(AgentFinishedPlanningNextStep::class)]
final class ZeroShotAgentTest extends TestCase
{
    private FakeLanguageModel $languageModel;
    private Callback $toolA;
    private Callback $toolB;

    protected function setUp(): void
    {
        $this->markTestSkipped();

        $this->languageModel = new FakeLanguageModel([
            Response::fromString("I should do something.\nAction: ToolA\nAction Input: Some input"),
            Response::fromString('Final Answer: 42'),
        ]);

        $this->toolA = new Callback('ToolA', 'ToolA description', fn (string $input): string => $input . ' - processed by ToolA');
        $this->toolB = new Callback('ToolB', 'ToolB description', fn (string $input): string => $input . ' - processed by ToolB');
    }

    public function testFromLLMAndTools(): void
    {
        $zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->languageModel, new Tools([$this->toolA, $this->toolB]));
        $this->assertInstanceOf(ZeroShotAgent::class, $zeroShotAgent);
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
        $agent = ZeroShotAgent::fromLLMAndTools($this->languageModel, new Tools([$this->toolA, $this->toolB]));
        $context = new Context(['question' => 'What is the meaning of life?']);

        $nextStep = $agent->plan($context);
        $this->assertInstanceOf(Action::class, $nextStep->action());
        $this->assertEquals('ToolA', $nextStep->action()->tool());
        $this->assertEquals('Some input', $nextStep->action()->input());

        $nextStep = $agent->plan($context, new Steps([$nextStep]));
        $this->assertInstanceOf(Finish::class, $nextStep->action());
        $this->assertEquals('42', $nextStep->action()->results()['answer']);
    }
}
