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
    private LanguageModelChain $languageModelChain;

    protected function setUp(): void
    {
        $this->languageModelChain = new LanguageModelChain(
            languageModel: new FakeLanguageModel([
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
        $output = $this->languageModelChain->process($input);

        $this->assertSame(['capital' => 'Paris'], $output->toArray());
    }

    public function testInputKeys(): void
    {
        $this->assertSame(['country'], $this->languageModelChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $this->assertSame(['capital'], $this->languageModelChain->outputKeys());
    }
}
