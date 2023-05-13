<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

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

        $this->assertSame($responsesToReturn[0], $fakeLanguageModel->generate('one'));
        $this->assertSame($responsesToReturn[1], $fakeLanguageModel->generate('two'));

        $this->expectException(\LogicException::class);
        $fakeLanguageModel->generate('three');
    }

    public function testAddResponse(): void
    {
        $response = Response::fromString('one');

        $fakeLanguageModel = new FakeLanguageModel();
        $fakeLanguageModel->addResponse($response);

        $this->assertSame($response, $fakeLanguageModel->generate('one'));
    }

    public function testCalls(): void
    {
        $response = Response::fromString('one');

        $fakeLanguageModel = new FakeLanguageModel();
        $fakeLanguageModel->addResponse($response);

        $fakeLanguageModel->generate('one', 'stop1', 'stop2');

        $this->assertEquals(['prompt' => 'one', 'stops' => ['stop1', 'stop2']], $fakeLanguageModel->calls()[0]);
    }
}
