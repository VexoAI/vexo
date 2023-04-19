<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use PHPUnit\Framework\TestCase;
use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\Prompt\Prompts;

final class FakeLLMTest extends TestCase
{
    public function testGenerate(): void
    {
        $responsesToReturn = [
            new Response([new Generation('one')]),
            new Response([new Generation('two')]),
        ];

        $fakeLLM = new FakeLLM($responsesToReturn);

        $this->assertSame($responsesToReturn[0], $fakeLLM->generate(new Prompts(new Prompt('one'))));
        $this->assertSame($responsesToReturn[1], $fakeLLM->generate(new Prompts(new Prompt('two'))));

        $this->expectException(\InvalidArgumentException::class);
        $fakeLLM->generate(new Prompts(new Prompt('three')));
    }

    public function testConstructorValidatesGenerations(): void
    {
        $responsesToReturn = [
            new Response([new Generation('one')]),
            new Response([new Generation('two')]),
            'Not a response'
        ];

        $this->expectException(\InvalidArgumentException::class);
        new FakeLLM($responsesToReturn);
    }
}
