<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\LLM\FakeLLM;
use Vexo\LLM\Response;
use Vexo\Prompt\BasicPromptTemplate;

#[CoversClass(LLMChain::class)]
final class LLMChainTest extends TestCase
{
    private LLMChain $llmChain;

    public function setUp(): void
    {
        $this->llmChain = new LLMChain(
            llm: new FakeLLM([
                Response::fromString('Paris'),
            ]),
            promptTemplate: new BasicPromptTemplate('What is the capital of {{country}}?', ['country']),
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
