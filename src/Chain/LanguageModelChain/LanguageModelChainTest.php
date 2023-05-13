<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\Prompt\BasicPromptTemplate;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;

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
            promptTemplate: new BasicPromptTemplate('What is the capital of {{country}}?', ['country'])
        );
    }

    public function testProcess(): void
    {
        $context = new Context(['country' => 'France']);

        $this->languageModelChain->run($context);

        $this->assertEquals('Paris', $context->get('text'));
    }

    public function testRequiredContextValues(): void
    {
        $this->assertEquals(
            ['country' => 'mixed'],
            $this->languageModelChain->requiredContextValues()
        );
    }
}
