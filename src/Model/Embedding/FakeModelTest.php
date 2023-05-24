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
        $model = new FakeModel();

        $vector = new Vector([0.01, -0.03, 0.04, -0.01]);
        $model->addVector($vector);

        $this->assertSame($vector, $model->embedQuery('What was the food like?'));
    }

    public function testEmbedQueryThrowsExceptionWhenOutOfVectors(): void
    {
        $model = new FakeModel();

        $this->expectException(\LogicException::class);
        $model->embedQuery('What was the food like?');
    }

    public function testEmbedTexts(): void
    {
        $model = new FakeModel();

        $vectors = [
            new Vector([0.01, -0.03, 0.04, -0.01]),
            new Vector([0.02, -0.04, 0.05, -0.02])
        ];

        $model->addVector($vectors[0]);
        $model->addVector($vectors[1]);

        $this->assertEquals(
            $vectors,
            $model->embedTexts([
                'The food was amazing and delicious.',
                'The service was slow but the food was great.'
            ])->toArray()
        );
    }

    public function testEmbedTextsThrowsExceptionWhenOutOfVectors(): void
    {
        $model = new FakeModel();

        $this->expectException(\LogicException::class);
        $model->embedTexts([
            'The food was amazing and delicious.',
            'The service was slow but the food was great.'
        ]);
    }
}
