<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent\MRKL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Agent\Action;
use Vexo\Weave\Agent\Finish;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\LLM\FakeLLM;
use Vexo\Weave\LLM\Response;
use Vexo\Weave\Tool\CallableTool;

#[CoversClass(ZeroShotAgent::class)]
final class ZeroShotAgentTest extends TestCase
{
    private FakeLLM $llm;
    private CallableTool $toolA;
    private CallableTool $toolB;

    protected function setUp(): void
    {
        $this->llm = new FakeLLM([
            Response::fromString("I should do something.\nAction: ToolA\nAction Input: Some input"),
            Response::fromString('Final Answer: 42'),
        ]);

        $this->toolA = new CallableTool('ToolA', 'ToolA description', fn (string $input) => $input . ' - processed by ToolA');
        $this->toolB = new CallableTool('ToolB', 'ToolB description', fn (string $input) => $input . ' - processed by ToolB');
    }

    public function testFromLLMAndTools(): void
    {
        $zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->llm, $this->toolA, $this->toolB);
        $this->assertInstanceOf(ZeroShotAgent::class, $zeroShotAgent);
    }

    public function testCreatePromptTemplate(): void
    {
        $promptTemplate = ZeroShotAgent::createPromptTemplate([$this->toolA, $this->toolB]);
        $renderedPrompt = $promptTemplate->render(['question' => 'What is the meaning of life?', 'scratchpad' => '']);
        $renderedTemplate = (string) $renderedPrompt;

        $this->assertStringContainsString('ToolA', $renderedTemplate);
        $this->assertStringContainsString('ToolB', $renderedTemplate);
    }

    public function testPlan(): void
    {
        $agent = ZeroShotAgent::fromLLMAndTools($this->llm, $this->toolA, $this->toolB);
        $input = new Input(['question' => 'What is the meaning of life?']);

        $nextStep = $agent->plan($input);
        $this->assertInstanceOf(Action::class, $nextStep->action());
        $this->assertEquals('ToolA', $nextStep->action()->tool());
        $this->assertEquals('Some input', $nextStep->action()->input());

        $nextStep = $agent->plan($input, [$nextStep]);
        $this->assertInstanceOf(Finish::class, $nextStep->action());
        $this->assertEquals('42', $nextStep->action()->results()['result']);
    }

    public function testPlanThrowsExceptionOnUnparsableOutput(): void
    {
        $unparsableLLM = new FakeLLM([
            Response::fromString('Unparsable output'),
        ]);

        $agent = ZeroShotAgent::fromLLMAndTools($unparsableLLM, $this->toolA, $this->toolB);
        $input = new Input(['question' => 'What is the meaning of life?']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not parse output');

        $agent->plan($input);
    }

}
