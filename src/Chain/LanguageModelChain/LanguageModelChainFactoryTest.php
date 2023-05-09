<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Input;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;
use Vexo\Prompt\Prompt;

#[CoversClass(LanguageModelChainFactory::class)]
final class LanguageModelChainFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $fakeLanguageModel = new FakeLanguageModel([Response::fromString('Paris')]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel);

        $languageModelChain = $languageModelChainFactory->create(
            promptTemplate: 'What is the capital of {{country}}?',
            stops: [],
            inputKeys: ['country'],
            outputKey: 'capital'
        );

        $output = $languageModelChain->process(new Input(['country' => 'France']));

        $this->assertSame(['country'], $languageModelChain->inputKeys());
        $this->assertSame(['capital'], $languageModelChain->outputKeys());
        $this->assertSame(['capital' => 'Paris'], $output->toArray());

        $call = $fakeLanguageModel->calls()[0];
        $this->assertInstanceOf(Prompt::class, $call['prompt']);
        $this->assertEquals('What is the capital of France?', (string) $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }

    public function testCreateFromBlueprint(): void
    {
        $fakeLanguageModel = new FakeLanguageModel([Response::fromString('Paris')]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel);

        $blueprint = new class() implements Blueprint {
            public function promptTemplate(): string
            {
                return 'What is the capital of {{country}}?';
            }

            public function stops(): array
            {
                return [];
            }

            public function inputKeys(): array
            {
                return ['country'];
            }

            public function outputKey(): string
            {
                return 'capital';
            }
        };

        $languageModelChain = $languageModelChainFactory->createFromBlueprint($blueprint);

        $output = $languageModelChain->process(new Input(['country' => 'France']));

        $this->assertSame(['country'], $languageModelChain->inputKeys());
        $this->assertSame(['capital'], $languageModelChain->outputKeys());
        $this->assertSame(['capital' => 'Paris'], $output->toArray());

        $call = $fakeLanguageModel->calls()[0];
        $this->assertInstanceOf(Prompt::class, $call['prompt']);
        $this->assertEquals('What is the capital of France?', (string) $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }
}
