<?php

declare(strict_types=1);

namespace Vexo\VectorStore\InMemoryVectorStore;

use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;

final class BasicLocalitySensitiveHashing implements LocalitySensitiveHashing
{
    private VectorsContract $hyperplanes;

    private array $hashBuckets = [];

    /**
     * @var callable
     */
    private $randomDimensionGenerator;

    public function __construct(
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20,
        ?callable $randomDimensionGenerator = null
    ) {
        $this->randomDimensionGenerator = $randomDimensionGenerator ?? fn (): float => random_int(-1000, 1000) / 1000;
        $this->generateHyperplanes();
    }

    public function project(string $id, VectorContract $vector): void
    {
        $this->hashBuckets[$this->generateHash($vector)][] = $id;
    }

    /**
     * @return array<string>
     */
    public function getCandidateIdsForVector(VectorContract $vector): array
    {
        return $this->hashBuckets[$this->generateHash($vector)] ?? [];
    }

    private function generateHyperplanes(): void
    {
        $this->hyperplanes = new Vectors();
        for ($i = 0; $i < $this->numHyperplanes; $i++) {
            $hyperplane = [];
            for ($j = 0; $j < $this->numDimensions; $j++) {
                $hyperplane[] = ($this->randomDimensionGenerator)();
            }
            $this->hyperplanes[] = new Vector($hyperplane);
        }
    }

    private function generateHash(VectorContract $vector): string
    {
        $hash = '';
        foreach ($this->hyperplanes as $hyperplane) {
            $hash .= $vector->similarity($hyperplane, $this->similarityAlgorithm) >= 0 ? '1' : '0';
        }

        return $hash;
    }
}
