<?php

declare(strict_types=1);

namespace Vexo\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Prompt\Prompt;

#[CoversClass(FakeLanguageModel::class)]
final class FakeLanguageModelTest extends TestCase
{
    public function testGenerate(): void
    {
        $responsesToReturn = [
            Response::fromString('one'),
            Response::fromString('two'),
        ];

        $fakeLLM = new FakeLanguageModel($responsesToReturn);

        $this->assertSame($responsesToReturn[0], $fakeLLM->generate(new Prompt('one')));
        $this->assertSame($responsesToReturn[1], $fakeLLM->generate(new Prompt('two')));

        $this->expectException(\InvalidArgumentException::class);
        $fakeLLM->generate(new Prompt('three'));
    }

    public function testConstructorValidatesCompletions(): void
    {
        $responsesToReturn = [
            Response::fromString('one'),
            Response::fromString('two'),
            'Not a response'
        ];

        $this->expectException(\InvalidArgumentException::class);
        new FakeLanguageModel($responsesToReturn);
    }
}
