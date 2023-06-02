<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\OutputParser\OutputParser;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\Renderer;
use Vexo\Chain\LanguageModelChain\Prompt\StrReplaceRenderer;
use Vexo\Model\Completion\FakeModel;
use Vexo\Model\Completion\Result;

#[CoversClass(LanguageModelChainFactory::class)]
final class LanguageModelChainFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $filesystem = vfsStream::setup('templates');
        vfsStream::newFile('prompt.twig')->at($filesystem)->setContent('What is the capital of {{ country }}?');

        $fakeLanguageModel = new FakeModel([new Result(['The capital of France is Paris'])]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel);

        $languageModelChain = $languageModelChainFactory->create(
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?'),
            outputParser: new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/'),
            stops: [],
        );

        $context = new Context(['country' => 'France']);
        $languageModelChain->run($context);

        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
        $this->assertEquals('Paris', $context->get('capital'));

        $call = $fakeLanguageModel->calls()[0];
        $this->assertEquals('What is the capital of France?', $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }

    public function testCreateFromBlueprint(): void
    {
        $filesystem = vfsStream::setup('templates');
        vfsStream::newFile('prompt.twig')->at($filesystem)->setContent('What is the capital of {{ country }}?');

        $fakeLanguageModel = new FakeModel([new Result(['The capital of France is Paris'])]);
        $languageModelChainFactory = new LanguageModelChainFactory($fakeLanguageModel);

        $blueprint = new class() implements Blueprint {
            public function promptRenderer(): Renderer
            {
                return new StrReplaceRenderer('What is the capital of {{country}}?');
            }

            public function outputParser(): OutputParser
            {
                return new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/');
            }

            public function stops(): array
            {
                return [];
            }
        };

        $languageModelChain = $languageModelChainFactory->createFromBlueprint($blueprint);

        $context = new Context(['country' => 'France']);
        $languageModelChain->run($context);

        $this->assertEquals('The capital of France is Paris', $context->get('generation'));
        $this->assertEquals('Paris', $context->get('capital'));

        $call = $fakeLanguageModel->calls()[0];
        $this->assertEquals('What is the capital of France?', $call['prompt']);
        $this->assertEquals([], $call['stops']);
    }
}
