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
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\Resolver\NameResolver;
use Vexo\Agent\Tool\Tools;
use Vexo\Chain\Context;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;

#[CoversClass(ZeroShotAgentExecutor::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorStartedProcessing::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorStartedRunIteration::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorFinishedProcessing::class)]
#[IgnoreClassForCodeCoverage(AgentExecutorForcedStop::class)]
final class ZeroShotAgentExecutorTest extends TestCase
{
    private FakeLanguageModel $languageModel;
    private Callback $toolA;
    private Callback $toolB;
    private NameResolver $toolResolver;
    private ZeroShotAgent $zeroShotAgent;
    private ZeroShotAgentExecutor $zeroShotAgentExecutor;

    protected function setUp(): void
    {
        $this->languageModel = new FakeLanguageModel([
            Response::fromString("I should do something.\nAction: ToolA\nAction Input: Some input"),
            Response::fromString('Final Answer: 42'),
        ]);

        $this->toolA = new Callback('toola', 'ToolA description', fn (string $input): string => $input . ' - processed by ToolA');
        $this->toolB = new Callback('toolb', 'ToolB description', fn (string $input): string => $input . ' - processed by ToolB');
        $this->toolResolver = new NameResolver(new Tools([$this->toolA, $this->toolB]));

        $this->zeroShotAgent = ZeroShotAgent::fromLLMAndTools($this->languageModel, new Tools([$this->toolA, $this->toolB]));
        $this->zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            $this->toolResolver
        );
    }

    public function testProcess(): void
    {
        $context = new Context(['question' => 'What is the meaning of life?']);
        $this->zeroShotAgentExecutor->run($context);

        $this->assertEquals(['answer' => 42], $context->get('results'));
        $this->assertCount(2, $context->get('intermediateSteps'));
    }

    public function testMaxIterationsReached(): void
    {
        $zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            $this->toolResolver,
            maxIterations: 1
        );

        $context = new Context(['question' => 'What is the meaning of life?']);
        $zeroShotAgentExecutor->run($context);

        $this->assertSame('Failed to answer question. Max iterations or time reached', $context->get('result'));
    }

    public function testMaxTimeReached(): void
    {
        $zeroShotAgentExecutor = new ZeroShotAgentExecutor(
            $this->zeroShotAgent,
            $this->toolResolver,
            maxTime: 0
        );

        $context = new Context(['question' => 'What is the meaning of life?']);
        $zeroShotAgentExecutor->run($context);

        $this->assertSame('Failed to answer question. Max iterations or time reached', $context->get('result'));
    }
}
