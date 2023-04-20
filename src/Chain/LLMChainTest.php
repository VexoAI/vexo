<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;
use Vexo\Weave\LLM\FakeLLM;
use Vexo\Weave\LLM\Generation;
use Vexo\Weave\LLM\Generations;
use Vexo\Weave\LLM\Response;
use Vexo\Weave\Prompt\StrReplaceRenderer;

final class LLMChainTest extends TestCase
{
    public function testProcess(): void
    {
        $llm = new FakeLLM([
            new Response(new Generations(new Generation('Paris'))),
        ]);
        $renderer = new StrReplaceRenderer();
        $promptTemplate = 'What is the capital of {{country}}?';
        $inputVariables = ['country'];
        $outputVariable = 'capital';

        $llmChain = new LLMChain($llm, $renderer, $promptTemplate, $inputVariables, $outputVariable);

        $input = new Input(['country' => 'France']);
        $output = $llmChain->process($input);

        $this->assertSame(['capital' => 'Paris'], $output->data());
    }
}
