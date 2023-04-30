<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Model\FakeLanguageModel;
use Vexo\Model\Response;
use Vexo\Prompt\BasicPromptTemplate;

#[CoversClass(LanguageModelChain::class)]
final class LanguageModelChainTest extends TestCase
{
    private LanguageModelChain $llmChain;

    protected function setUp(): void
    {
        $this->llmChain = new LanguageModelChain(
            llm: new FakeLanguageModel([
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

        $this->assertSame(['capital' => 'Paris'], $output->toArray());
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
