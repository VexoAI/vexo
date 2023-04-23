<?php

declare(strict_types=1);

namespace Vexo\Weave\Agent\MRKL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;
use Vexo\Weave\Chain\Output;
use Vexo\Weave\LLM\FakeLLM;
use Vexo\Weave\LLM\Generation;
use Vexo\Weave\LLM\Generations;
use Vexo\Weave\LLM\Response;
use Vexo\Weave\Tool\CallableTool;

#[CoversClass(ZeroShotAgentExecutor::class)]
final class ZeroShotAgentExecutorTest extends TestCase
{
    private FakeLLM $llm;
    private CallableTool $toolA;
    private CallableTool $toolB;
    private ZeroShotAgent $zeroShotAgent;
    private ZeroShotAgentExecutor $zeroShotAgentExecutor;

    protected function setUp(): void
    {
        $this->llm = new FakeLLM([
            new Response(new Generations(new Generation("I should do something.\nAction: ToolA\nAction Input: Some input"))),
            new Response(new Generations(new Generation('Final Answer: 42'))),
        ]);

        $this->toolA = new CallableTool('toola', 'ToolA description', fn (string $input) => $input . ' - processed by ToolA');
        $this->toolB = new CallableTool('toolb', 'ToolB description', fn (string $input) => $input . ' - processed by ToolB');

        $this->zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->llm, $this->toolA, $this->toolB);
        $this->zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            [$this->toolA->name() => $this->toolA, $this->toolB->name() => $this->toolB]
        );
    }

    public function testProcess(): void
    {
        $input = new Input(['question' => 'What is the meaning of life?']);
        $output = $this->zeroShotAgentExecutor->process($input);

        $this->assertInstanceOf(Output::class, $output);
        $this->assertSame('42', $output->get('result'));
        $this->assertCount(2, $output->get('intermediateSteps'));
    }

    public function testMaxIterationsReached(): void
    {
        $zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            [$this->toolA->name() => $this->toolA, $this->toolB->name() => $this->toolB],
            maxIterations: 1
        );

        $input = new Input(['question' => 'What is the meaning of life?']);
        $output = $zeroShotAgentExecutor->process($input);

        $this->assertSame('Failed to answer question. Max iterations or time reached', $output->get('result'));
    }

    public function testMaxTimeReached(): void
    {
        $zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            [$this->toolA->name() => $this->toolA, $this->toolB->name() => $this->toolB],
            maxTime: 0
        );

        $input = new Input(['question' => 'What is the meaning of life?']);
        $output = $zeroShotAgentExecutor->process($input);

        $this->assertSame('Failed to answer question. Max iterations or time reached', $output->get('result'));
    }

    public function testInputKeys(): void
    {
        $inputKeys = $this->zeroShotAgentExecutor->inputKeys();

        $this->assertIsArray($inputKeys);
        $this->assertSame(['question'], $inputKeys);
    }

    public function testOutputKeys(): void
    {
        $outputKeys = $this->zeroShotAgentExecutor->outputKeys();

        $this->assertIsArray($outputKeys);
        $this->assertSame(['result', 'intermediateSteps'], $outputKeys);
    }
}
