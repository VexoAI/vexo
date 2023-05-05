<?php

declare(strict_types=1);

namespace Vexo\Compare;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Similarity::class)]
final class SimilarityTest extends TestCase
{
    public function testCosineSimilarityIsCorrect(): void
    {
        $this->assertEqualsWithDelta(1, Similarity::cosine([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]), 0.00001);
        $this->assertEquals(0.8951435925492909, Similarity::cosine([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testCzekanowskiSimilarityReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(1, Similarity::czekanowski([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testCzekanowskiSimilarityIsCorrect(): void
    {
        $this->assertEquals(0.7999999999999999, Similarity::czekanowski([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
        $this->assertEqualsWithDelta(1 - Distance::sorensen([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), Similarity::czekanowski([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00001);
    }

    public function testDiceSimilarityIsCorrect(): void
    {
        $this->assertEquals(50/56, Similarity::dice([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testIntersectionSimilarityReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(1, Similarity::intersection([0.3, 0.2, 0.3, 0.2], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testIntersectionSimilarityIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.8, Similarity::intersection([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.0001);
        $this->assertEqualsWithDelta(1 - Distance::manhattan([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]) / 2, Similarity::intersection([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.0001);
    }

    public function testKulczynskiSimilarityIsCorrect(): void
    {
        $this->assertEquals(1.9999999999999996, Similarity::kulczynski([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testKumarHassebrookSimilarityIsCorrect(): void
    {
        $this->assertEquals(1, Similarity::kumarHassebrook([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
        $this->assertEquals(0.8064516129032256, Similarity::kumarHassebrook([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testMotykaSimilarityReturnsHalfWithEqualVectors(): void
    {
        $this->assertEquals(0.5, Similarity::motyka([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testMotykaSimilarityIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.4, Similarity::motyka([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.001);
    }

    public function testPearsonCorrelationIsCorrect(): void
    {
        $this->assertEqualsWithDelta(1, Similarity::pearson([0, 1, 2, 3], [0, 1, 2, 3]), 0.00000001);
        $this->assertEqualsWithDelta(0.6324555320336759, Similarity::pearson([0, 1, 2, 3], [0, 1, 2, 1]), 0.00000001);
    }

    public function testSquaredChordSimilarityIsCorrect(): void
    {
        $this->assertEquals(0.9384260859804927, Similarity::squaredChord([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testTanimotoSimilarityReturnsOneWithEqualVectors(): void
    {
        $this->assertEquals(1, Similarity::tanimoto([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
        $this->assertEquals(1, Similarity::tanimoto([1, 0, 1], [1, 0, 1], true));
    }

    public function testTanimotoSimilarityIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.666, Similarity::tanimoto([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.01);
        $this->assertEqualsWithDelta(0.666, Similarity::tanimoto([1, 0, 1], [1, 1, 1], true), 0.01);
    }
}
