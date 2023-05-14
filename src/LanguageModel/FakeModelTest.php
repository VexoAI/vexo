<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeModel::class)]
final class FakeModelTest extends TestCase
{
    public function testGenerate(): void
    {
        $resultsToReturn = [
            new Result(['one']),
            new Result(['two']),
        ];

        $fakeLanguageModel = new FakeModel($resultsToReturn);

        $this->assertSame($resultsToReturn[0], $fakeLanguageModel->generate('one'));
        $this->assertSame($resultsToReturn[1], $fakeLanguageModel->generate('two'));

        $this->expectException(\LogicException::class);
        $fakeLanguageModel->generate('three');
    }

    public function testAddResult(): void
    {
        $result = new Result(['one']);

        $fakeLanguageModel = new FakeModel();
        $fakeLanguageModel->addResult($result);

        $this->assertSame($result, $fakeLanguageModel->generate('one'));
    }

    public function testCalls(): void
    {
        $result = new Result(['one']);

        $fakeLanguageModel = new FakeModel();
        $fakeLanguageModel->addResult($result);

        $fakeLanguageModel->generate('one', ['stop1', 'stop2']);

        $this->assertEquals(['prompt' => 'one', 'stops' => ['stop1', 'stop2']], $fakeLanguageModel->calls()[0]);
    }
}
