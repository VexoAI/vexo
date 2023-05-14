<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Context;
use Vexo\Chain\LanguageModelChain\OutputParser\RegexOutputParser;
use Vexo\Chain\LanguageModelChain\Prompt\StrReplaceRenderer;
use Vexo\LanguageModel\FakeModel;
use Vexo\LanguageModel\Response;

#[CoversClass(LanguageModelChain::class)]
final class LanguageModelChainTest extends TestCase
{
    private LanguageModelChain $languageModelChain;

    protected function setUp(): void
    {
        $this->languageModelChain = new LanguageModelChain(
            languageModel: new FakeModel([
                Response::fromString('The capital of France is Paris'),
            ]),
            promptRenderer: new StrReplaceRenderer('What is the capital of {{country}}?'),
            outputParser: new RegexOutputParser('/^The capital of (.*) is (?<capital>.*)$/'),
            requiredContextValues: ['country']
        );
    }

    public function testProcess(): void
    {
        $context = new Context(['country' => 'France']);

        $this->languageModelChain->run($context);

        $this->assertEquals('The capital of France is Paris', $context->get('completions'));
        $this->assertEquals('Paris', $context->get('capital'));
    }

    public function testRequiredContextValues(): void
    {
        $this->assertEquals(
            ['country' => 'mixed'],
            $this->languageModelChain->requiredContextValues()
        );
    }
}
