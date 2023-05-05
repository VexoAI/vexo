<?php

declare(strict_types=1);

namespace Vexo\Compare;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Distance::class)]
final class DistanceTest extends TestCase
{
    public function testAdditiveSymmetricReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::additiveSymmetric([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testAdditiveSymmetricIsCorrect(): void
    {
        $this->assertEqualsWithDelta(40.694444, Distance::additiveSymmetric([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testAvgReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::avg([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testAvgIsCorrect(): void
    {
        $this->assertEquals(10.5, Distance::avg([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testBhattacharyyaIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.031270832649666964, Distance::bhattacharyya([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.0000000001);
    }

    public function testCanberraReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::canberra([3, 6, 9, 4, 3], [3, 6, 9, 4, 3]));
    }

    public function testCanberraIsCorrect(): void
    {
        $this->assertEquals(2.4989010989010993, Distance::canberra([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testChebyshevReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::chebyshev([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testChebyshevIsCorrect(): void
    {
        $this->assertEquals(5, Distance::chebyshev([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testClarkReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::clark([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testClarkIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.85914671, Distance::clark([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.0000001);
    }

    public function testCzekanowskiReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::czekanowski([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testCzekanowskiIsCorrect(): void
    {
        $this->assertEquals(0.20000000000000007, Distance::czekanowski([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
        $this->assertEqualsWithDelta(Distance::sorensen([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), Distance::czekanowski([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00001);
    }

    public function testDiceReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::dice([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testDiceIsCorrect(): void
    {
        $this->assertEquals(6 / 56, Distance::dice([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testDivergenceReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::divergence([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testDivergenceIsCorrect(): void
    {
        $this->assertEquals(1.4762661514309867, Distance::divergence([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testEuclideanReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::euclidean([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testEuclideanIsCorrect(): void
    {
        $this->assertEquals(8, Distance::euclidean([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testFidelityIsCorrect(): void
    {
        $this->assertEquals(0.9692130429902465, Distance::fidelity([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testGowerReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::gower([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testGowerIsCorrect(): void
    {
        $this->assertEquals(16 / 5, Distance::gower([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testHarmonicMeanIsCorrect(): void
    {
        $this->assertEquals(0.94, Distance::harmonicMean([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testHellingerIsCorrect(): void
    {
        $this->assertEquals(0.3509242482915852, Distance::hellinger([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testInnerProductIsCorrect(): void
    {
        $this->assertEquals(0.25, Distance::innerProduct([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testIntersectionReturnsZeroWithEqualVectors(): void
    {
        $this->assertEqualsWithDelta(0, Distance::intersection([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]), 1e-15);
    }

    public function testIntersectionIsCorrect(): void
    {
        $this->assertEquals(0.20000000000000007, Distance::intersection([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
        $this->assertEqualsWithDelta(Distance::manhattan([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]) / 2, Distance::intersection([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00001);
    }

    public function testJaccardIsCorrect(): void
    {
        $this->assertEquals(0, Distance::jaccard([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
        $this->assertEqualsWithDelta(6 / 31, Distance::jaccard([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.0000000001);
    }

    public function testJeffreysIsCorrect(): void
    {
        $this->assertEquals(0.2484906649788, Distance::jeffreys([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testJensenDifferenceIsCorrect(): void
    {
        $this->assertEquals(0.030518733906981843, Distance::jensenDifference([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testJensenShannonIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.0305187339069818, Distance::jensenShannon([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testKdivergenceIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.029897607907053952, Distance::kdivergence([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testKulczynskiReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::kulczynski([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testKulczynskiIsCorrect(): void
    {
        $this->assertEqualsWithDelta(16 / 11, Distance::kulczynski([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testKullbackLeiblerIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.12685113254635072, Distance::kullbackLeibler([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testKumarJohnsonIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.5623488044808911, Distance::kumarJohnson([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testLorentzianReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::lorentzian([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testLorentzianIsCorrect(): void
    {
        $this->assertEqualsWithDelta(log(864), Distance::lorentzian([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testManhattanReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::manhattan([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testManhattanIsCorrect(): void
    {
        $this->assertEquals(16, Distance::manhattan([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]));
    }

    public function testMatusitaSimilarity(): void
    {
        $this->assertEqualsWithDelta(0.24814091564977162, Distance::matusita([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testMinkowskiReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::minkowski([0, 1, 4, 6, 2], [0, 1, 4, 6, 2], 1));
        $this->assertEquals(0, Distance::minkowski([0, 1, 4, 6, 2], [0, 1, 4, 6, 2], 2));
        $this->assertEquals(0, Distance::minkowski([0, 1, 4, 6, 2], [0, 1, 4, 6, 2], 5));
    }

    public function testMinkowskiIsCorrect(): void
    {
        $this->assertEquals(Distance::manhattan([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), Distance::minkowski([0, 1, 4, 6, 2], [3, 6, 9, 4, 3], 1));
        $this->assertEquals(Distance::euclidean([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), Distance::minkowski([0, 1, 4, 6, 2], [3, 6, 9, 4, 3], 2));
        $this->assertEquals(6526 ** (1 / 5), Distance::minkowski([0, 1, 4, 6, 2], [3, 6, 9, 4, 3], 5));
    }

    public function testMotykaReturnsHalfWithEqualVectors(): void
    {
        $this->assertEquals(0.5, Distance::motyka([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testMotykaIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.6000000000000001, Distance::motyka([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testNeymanReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::neyman([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testNeymanIsCorrect(): void
    {
        $this->assertEqualsWithDelta(32.41666666666667, Distance::neyman([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testPearsonReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::pearson([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testPearsonIsCorrect(): void
    {
        $this->assertEqualsWithDelta(8.277777777777779, Distance::pearson([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testProbabilisticSymmetricReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::probabilisticSymmetric([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testProbabilisticSymmetricIsCorrect(): void
    {
        $this->assertEqualsWithDelta(12.18901098901099, Distance::probabilisticSymmetric([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
        $this->assertEqualsWithDelta(2 * Distance::squared([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), Distance::probabilisticSymmetric([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testRuzickaIsCorrect(): void
    {
        $this->assertEquals(2 / 3, Distance::ruzicka([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]));
    }

    public function testSoergelReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::soergel([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testSoergelIsCorrect(): void
    {
        $this->assertEqualsWithDelta(16 / 27, Distance::soergel([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testSorensenReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::sorensen([0, 1, 4, 6, 2], [0, 1, 4, 6, 2]));
    }

    public function testSorensenIsCorrect(): void
    {
        $this->assertEqualsWithDelta(16 / 38, Distance::sorensen([0, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testSquaredReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::squared([3, 1, 4, 6, 2], [3, 1, 4, 6, 2]));
    }

    public function testSquaredIsCorrect(): void
    {
        $this->assertEqualsWithDelta(6.094505494505495, Distance::squared([3, 1, 4, 6, 2], [3, 6, 9, 4, 3]), 0.00005);
    }

    public function testSquaredChordIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.06157391401950735, Distance::squaredChord([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testTanejaIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.031603932337718146, Distance::taneja([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testTanimotoReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::tanimoto([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
        $this->assertEquals(0, Distance::tanimoto([1, 0, 1], [1, 0, 1], true));
    }

    public function testTanimotoIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.33333333333333337, Distance::tanimoto([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
        $this->assertEqualsWithDelta(0.33333333333333337, Distance::soergel([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
        $this->assertEqualsWithDelta(0.333, Distance::tanimoto([1, 0, 1], [1, 1, 1], true), 0.1);
    }

    public function testTopsoeIsCorrect(): void
    {
        $this->assertEqualsWithDelta(0.0610374678139636, Distance::topsoe([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }

    public function testWaveHedgesReturnsZeroWithEqualVectors(): void
    {
        $this->assertEquals(0, Distance::waveHedges([0.2, 0.4, 0.3, 0.1], [0.2, 0.4, 0.3, 0.1]));
    }

    public function testWaveHedgesIsCorrect(): void
    {
        $this->assertEqualsWithDelta(4 / 3, Distance::waveHedges([0.2, 0.4, 0.3, 0.1], [0.3, 0.2, 0.3, 0.2]), 0.00005);
    }
}
