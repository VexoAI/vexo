<?php

declare(strict_types=1);

namespace Vexo\Agent\MRKL;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\AgentExecutorFinishedProcessing;
use Vexo\Agent\AgentExecutorForcedStop;
use Vexo\Agent\AgentExecutorStartedProcessing;
use Vexo\Agent\AgentExecutorStartedRunIteration;
use Vexo\Chain\Input;
use Vexo\Chain\Output;
use Vexo\LLM\FakeLLM;
use Vexo\LLM\Response;
use Vexo\Tool\Callback;
use Vexo\Tool\Resolver\NameResolver;

#[CoversClass(ZeroShotAgentExecutor::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorStartedProcessing::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorStartedRunIteration::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorFinishedProcessing::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorForcedStop::class)]
final class ZeroShotAgentExecutorTest extends TestCase
{
    private FakeLLM $llm;
    private Callback $toolA;
    private Callback $toolB;
    private NameResolver $toolResolver;
    private ZeroShotAgent $zeroShotAgent;
    private ZeroShotAgentExecutor $zeroShotAgentExecutor;

    protected function setUp(): void
    {
        $this->llm = new FakeLLM([
            Response::fromString("I should do something.\nAction: ToolA\nAction Input: Some input"),
            Response::fromString('Final Answer: 42'),
        ]);

        $this->toolA = new Callback('toola', 'ToolA description', fn (string $input) => $input . ' - processed by ToolA');
        $this->toolB = new Callback('toolb', 'ToolB description', fn (string $input) => $input . ' - processed by ToolB');
        $this->toolResolver = new NameResolver([$this->toolA, $this->toolB]);

        $this->zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->llm, [$this->toolA, $this->toolB]);
        $this->zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            $this->toolResolver
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
            $this->toolResolver,
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
            $this->toolResolver,
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
