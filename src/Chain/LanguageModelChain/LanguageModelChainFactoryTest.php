<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\Prompt\Prompt;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;

#[CoversClass(LanguageModelChainFactory::class)]
final class LanguageModelChainFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $filesystem = vfsStream::setup('templates');
        vfsStream::newFile('prompt.twig')->at($filesystem)->setContent('What is the capital of {{ country }}?');

        $fakeLanguageModel = new FakeLanguageModel([Response::fromString('Paris')]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel, $filesystem->url());

        $languageModelChain = $languageModelChainFactory->create(
            promptTemplate: 'prompt.twig',
            requiredContextValues: ['country'],
            stops: [],
        );

        $context = new Context(['country' => 'France']);
        $languageModelChain->run($context);

        $this->assertSame(['country' => 'mixed'], $languageModelChain->requiredContextValues());
        $this->assertSame('Paris', $context->get('text'));

        $call = $fakeLanguageModel->calls()[0];
        $this->assertEquals('What is the capital of France?', $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }

    public function testCreateFromBlueprint(): void
    {
        $filesystem = vfsStream::setup('templates');
        vfsStream::newFile('prompt.twig')->at($filesystem)->setContent('What is the capital of {{ country }}?');

        $fakeLanguageModel = new FakeLanguageModel([Response::fromString('Paris')]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel, $filesystem->url());

        $blueprint = new class() implements Blueprint {
            public function promptTemplate(): string
            {
                return 'prompt.twig';
            }

            public function requiredContextValues(): array
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
        $this->assertEquals('What is the capital of France?', $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }
}
