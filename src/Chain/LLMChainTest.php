<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;
use Vexo\Weave\LLM\FakeLLM;
use Vexo\Weave\LLM\Generation;
use Vexo\Weave\LLM\Generations;
use Vexo\Weave\LLM\Response;

final class LLMChainTest extends TestCase
{
    public function testProcess(): void
    {
        $llm = new FakeLLM([
            new Response(new Generations(new Generation('Paris'))),
        ]);
        $llmChain = new LLMChain($llm);

        $input = new Input(['prompt' => 'What is the capital of France?']);
        $output = $llmChain->process($input);

        $this->assertSame(['text' => 'Paris'], $output->data());
    }
}
