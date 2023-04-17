<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use PHPUnit\Framework\TestCase;
use Pragmatist\Assistant\Prompt\Prompt;

final class FakeLLMTest extends TestCase
{
    public function testGenerate(): void
    {
        $responsesToReturn = [
            new Response([new Generation('one')]),
            new Response([new Generation('two')]),
        ];

        $fakeLLM = new FakeLLM($responsesToReturn);

        $this->assertSame($responsesToReturn[0], $fakeLLM->generate(new Prompt('one')));
        $this->assertSame($responsesToReturn[1], $fakeLLM->generate(new Prompt('two')));

        $this->expectException(\InvalidArgumentException::class);
        $fakeLLM->generate(new Prompt('three'));
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
