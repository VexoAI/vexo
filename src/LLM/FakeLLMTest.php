<?php

declare(strict_types=1);

namespace Vexo\LLM;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Prompt\Prompt;

#[CoversClass(FakeLLM::class)]
final class FakeLLMTest extends TestCase
{
    public function testGenerate(): void
    {
        $responsesToReturn = [
            Response::fromString('one'),
            Response::fromString('two'),
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
            Response::fromString('one'),
            Response::fromString('two'),
            'Not a response'
        ];

        $this->expectException(\InvalidArgumentException::class);
        new FakeLLM($responsesToReturn);
    }
}
