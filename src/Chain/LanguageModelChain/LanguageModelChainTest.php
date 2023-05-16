<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\StrReplaceRenderer;
use Vexo\Model\Language\FakeModel;
use Vexo\Model\Language\Result;

#[CoversClass(LanguageModelChain::class)]
final class LanguageModelChainTest extends TestCase
{
    public function testProcess(): void
    {
        $languageModelChain = new LanguageModelChain(
            languageModel: new FakeModel([
                new Result(['The capital of France is Paris']),
            ]),
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?'),
            outputParser: new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/')
        );

        $context = new Context(['country' => 'France']);

        $languageModelChain->run($context);

        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
        $this->assertEquals('Paris', $context->get('capital'));
    }

    public function testProcessWithoutOutputParser(): void
    {
        $languageModelChain = new LanguageModelChain(
            languageModel: new FakeModel([
                new Result(['The capital of France is Paris']),
            ]),
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?')
        );

        $context = new Context(['country' => 'France']);

        $languageModelChain->run($context);

        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
    }

    public function testProcessWithInputMap(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?'),
            inputMap: ['country' => 'nation']
        );

        $context = new Context(['nation' => 'France']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
    }
}
