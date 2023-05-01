<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

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

        $fakeLanguageModel = new FakeLanguageModel($responsesToReturn);

        $this->assertSame($responsesToReturn[0], $fakeLanguageModel->generate(new Prompt('one')));
        $this->assertSame($responsesToReturn[1], $fakeLanguageModel->generate(new Prompt('two')));

        $this->expectException(\LogicException::class);
        $fakeLanguageModel->generate(new Prompt('three'));
    }

    public function testAddResponse(): void
    {
        $response = Response::fromString('one');

        $fakeLanguageModel = new FakeLanguageModel();
        $fakeLanguageModel->addResponse($response);

        $this->assertSame($response, $fakeLanguageModel->generate(new Prompt('one')));
    }
}
