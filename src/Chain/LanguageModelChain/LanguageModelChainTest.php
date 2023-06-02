<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\FailedToValidateContextValue;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\StrReplaceRenderer;
use Vexo\Model\Completion\FakeModel;
use Vexo\Model\Completion\Result;

#[CoversClass(LanguageModelChain::class)]
final class LanguageModelChainTest extends TestCase
{
    public function testProcess(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel
        );

        $context = new Context(['prompt' => 'What is the capital of France?']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
    }

    public function testProcessThrowsExceptionOnInvalidPrompt(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel
        );

        $context = new Context(['prompt' => '']); // Very invalid prompt

        $this->expectException(FailedToValidateContextValue::class);
        $languageModelChain->run($context);
    }

    public function testProcessWithPromptRenderer(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?')
        );

        $context = new Context(['country' => 'France']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
    }

    public function testProcessWithOutputParser(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            outputParser: new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/')
        );

        $context = new Context(['prompt' => 'What is the capital of France?']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
        $this->assertEquals('Paris', $context->get('capital'));
    }

    public function testProcessWithInputMapAndPromptRenderer(): void
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

    public function testProcessWithInputMapWithoutPromptRenderer(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            inputMap: ['prompt' => 'instruction']
        );

        $context = new Context(['instruction' => 'What is the capital of France?']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
    }

    public function testProcessWithOutputMapAndOutputParser(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            outputParser: new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/'),
            outputMap: ['capital' => 'city']
        );

        $context = new Context(['prompt' => 'What is the capital of France?']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
        $this->assertEquals('Paris', $context->get('city'));
    }

    public function testProcessWithOutputMapWithoutOutputParser(): void
    {
        $fakeModel = new FakeModel([
            new Result(['The capital of France is Paris']),
        ]);

        $languageModelChain = new LanguageModelChain(
            languageModel: $fakeModel,
            outputMap: ['generation' => 'text']
        );

        $context = new Context(['prompt' => 'What is the capital of France?']);

        $languageModelChain->run($context);

        $this->assertEquals('What is the capital of France?', $fakeModel->calls()[0]['prompt']);
        $this->assertEquals('The capital of France is Paris', $context->get('text'));
    }

    public function testProcessThrowsExceptionWhenModelThrowsException(): void
    {
        $languageModelChain = new LanguageModelChain(
            languageModel: new FakeModel(),
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?')
        );

        $context = new Context(['country' => 'France']);

        $this->expectException(ModelFailedToGenerateResult::class);
        $languageModelChain->run($context);
    }
}
