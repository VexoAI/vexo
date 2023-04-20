<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\LLM\FakeLLM;
use Vexo\Weave\LLM\Generation;
use Vexo\Weave\LLM\Generations;
use Vexo\Weave\LLM\Response;
use Vexo\Weave\Prompt\StrReplaceRenderer;

#[CoversClass(LLMChain::class)]
final class LLMChainTest extends TestCase
{
    private LLMChain $llmChain;

    public function setUp(): void
    {
        $this->llmChain = new LLMChain(
            llm: new FakeLLM([
                new Response(new Generations(new Generation('Paris'))),
            ]),
            promptRenderer: new StrReplaceRenderer(),
            promptTemplate: 'What is the capital of {{country}}?',
            inputKeys: ['country'],
            outputKey: 'capital'
        );
    }

    public function testProcess(): void
    {
        $input = new Input(['country' => 'France']);
        $output = $this->llmChain->process($input);

        $this->assertSame(['capital' => 'Paris'], $output->data());
    }

    public function testInputKeys(): void
    {
        $this->assertSame(['country'], $this->llmChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $this->assertSame(['capital'], $this->llmChain->outputKeys());
    }
}
