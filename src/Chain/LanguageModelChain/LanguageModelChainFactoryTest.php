<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Prompt\Prompt;
use Vexo\LanguageModel\Response;

#[CoversClass(LanguageModelChainFactory::class)]
final class LanguageModelChainFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $fakeLanguageModel = new FakeLanguageModel([Response::fromString('Paris')]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel);

        $languageModelChain = $languageModelChainFactory->create(
            promptTemplate: 'What is the capital of {{country}}?',
            promptVariables: ['country'],
            stops: [],
        );

        $context = new Context(['country' => 'France']);
        $languageModelChain->run($context);

        $this->assertSame(['country' => 'mixed'], $languageModelChain->requiredContextValues());
        $this->assertSame('Paris', $context->get('text'));

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

            public function promptVariables(): array
            {
                return ['country'];
            }

            public function stops(): array
            {
                return [];
            }
        };

        $languageModelChain = $languageModelChainFactory->createFromBlueprint($blueprint);

        $context = new Context(['country' => 'France']);
        $languageModelChain->run($context);

        $this->assertSame(['country' => 'mixed'], $languageModelChain->requiredContextValues());
        $this->assertSame('Paris', $context->get('text'));

        $call = $fakeLanguageModel->calls()[0];
        $this->assertInstanceOf(Prompt::class, $call['prompt']);
        $this->assertEquals('What is the capital of France?', (string) $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }
}
