<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Vector\Vector;

#[CoversClass(FakeModel::class)]
final class FakeModelTest extends TestCase
{
    public function testEmbedQuery(): void
    {
        $fakeModel = new FakeModel();

        $fakeModel->addVector(new Vector([0.5, -0.5, 0.25]));

        $this->assertSame([0.5, -0.5, 0.25], $fakeModel->embedQuery('one')->toArray());
    }

    public function testEmbedTexts(): void
    {
        $fakeModel = new FakeModel();

        $fakeModel->addVector(new Vector([0.5, -0.5, 0.25]));
        $fakeModel->addVector(new Vector([-0.25, 0.5, -0.5]));
        $fakeModel->addVector(new Vector([0.25, -0.25, 0.5]));

        $vectors = $fakeModel->embedTexts(['one', 'two', 'three']);

        $this->assertCount(3, $vectors);
        $this->assertSame([0.5, -0.5, 0.25], $vectors[0]->toArray());
        $this->assertSame([-0.25, 0.5, -0.5], $vectors[1]->toArray());
        $this->assertSame([0.25, -0.25, 0.5], $vectors[2]->toArray());
    }

    public function testEmbedQueryThrowsExceptionWhenNoMoreVectors(): void
    {
        $fakeModel = new FakeModel();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No more vectors to return.');

        $fakeModel->embedQuery('one');
    }

    public function testEmbedTextsThrowsExceptionWhenNoMoreVectors(): void
    {
        $fakeModel = new FakeModel();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('No more vectors to return.');

        $fakeModel->embedTexts(['one']);
    }
}
