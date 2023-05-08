<?php

declare(strict_types=1);

namespace Vexo\VectorStore\InMemoryVectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Contract\Vector\Implementation\Vector;

#[CoversClass(BasicLocalitySensitiveHashing::class)]
final class BasicLocalitySensitiveHashingTest extends TestCase
{
    public function testProjection(): void
    {
        $randomDimensionGenerator = function (): float {
            static $values = [0.75, 0.2, -0.1];

            return array_shift($values);
        };

        $lsh = new BasicLocalitySensitiveHashing(
            randomDimensionGenerator: $randomDimensionGenerator,
            numHyperplanes: 1,
            numDimensions: 3
        );

        $lsh->project('id-1', new Vector([0.25, -0.25, 0.25])); // Hash: 1
        $lsh->project('id-2', new Vector([0.8, 0.3, -0.25])); // Hash: 1
        $lsh->project('id-3', new Vector([-0.25, 0.25, -0.25])); // Hash: 0
        $lsh->project('id-4', new Vector([-0.25, -0.25, 0.25])); // Hash: 0

        $this->assertEquals(['id-1', 'id-2'], $lsh->getCandidateIdsForVector(new Vector([0.25, -0.25, 0.25])));
        $this->assertEquals(['id-3', 'id-4'], $lsh->getCandidateIdsForVector(new Vector([-0.25, 0.25, -0.25])));
    }
}
