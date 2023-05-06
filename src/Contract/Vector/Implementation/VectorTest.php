<?php

declare(strict_types=1);

namespace Vexo\Contract\Vector\Implementation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Vector\DistanceAlgorithm;
use Vexo\Contract\Vector\SimilarityAlgorithm;

#[CoversClass(Vector::class)]
final class VectorTest extends TestCase
{
    private Vector $vector;

    protected function setUp(): void
    {
        $this->vector = new Vector([1, 2, 3]);
    }

    public function testToArray(): void
    {
        $this->assertEquals([1, 2, 3], $this->vector->toArray());
    }

    public function testFromArray(): void
    {
        $this->assertEquals([4, 5, 6], Vector::fromArray([4, 5, 6])->toArray());
    }

    public function testDistance(): void
    {
        $anotherVector = new Vector([4, 5, 6]);

        $this->assertEqualsWithDelta(5.19615, $this->vector->distance($anotherVector, DistanceAlgorithm::EUCLIDEAN), 0.00005);
    }

    public function testSimilarity(): void
    {
        $anotherVector = new Vector([4, 5, 6]);

        $this->assertEqualsWithDelta(0.97463, $this->vector->similarity($anotherVector, SimilarityAlgorithm::COSINE), 0.00005);
    }
}
