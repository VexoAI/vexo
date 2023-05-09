<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use Vexo\Contract\Document\Document as DocumentContract;
use Vexo\Contract\Document\Documents as DocumentsContract;
use Vexo\Contract\Document\Implementation\Document;
use Vexo\Contract\Document\Implementation\Documents;
use Vexo\Contract\Metadata\Implementation\Metadata;
use Vexo\Contract\Metadata\Metadata as MetadataContract;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\SimilarityAlgorithm;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;
use Vexo\EmbeddingModel\EmbeddingModel;

final class InMemoryVectorStore implements WritableVectorStore, SearchableVectorStore
{
    private readonly VectorsContract $hyperplanes;

    /**
     * @var array<string, array<int, array{contents: string, metadata: MetadataContract, vector: VectorContract}>>
     */
    private array $hashBuckets = [];

    /**
     * @var callable
     */
    private $randomDimensionGenerator;

    public function __construct(
        private readonly EmbeddingModel $embeddingModel,
        private readonly SimilarityAlgorithm $similarityAlgorithm = SimilarityAlgorithm::COSINE,
        private readonly int $numDimensions = 1536,
        private readonly int $numHyperplanes = 20,
        ?callable $randomDimensionGenerator = null
    ) {
        $this->randomDimensionGenerator = $randomDimensionGenerator ?? fn (): float => random_int(-1000, 1000) / 1000;
        $this->hyperplanes = $this->generateHyperplanes();
    }

    public function add(DocumentContract $document): void
    {
        $embedding = $this->embeddingModel
            ->embedTexts([$document->contents()])
            ->first();

        $this->hashBuckets[$this->generateHash($embedding)][] = [
            'contents' => $document->contents(),
            'metadata' => $document->metadata(),
            'vector' => $embedding
        ];
    }

    public function search(string $query, int $numResults = 4): DocumentsContract
    {
        // Embed the query
        $queryVector = $this->embeddingModel->embedQuery($query);

        // Retrieve the vectors that have the same LSH hash as the query
        $candidates = $this->hashBuckets[$this->generateHash($queryVector)] ?? [];

        // Initialize priority queue to keep track of the highest scoring vectors
        $priorityQueue = new \SplPriorityQueue();

        // Iterate over the candidate vectors and calculate the similarity between the query and the candidate vectors
        foreach ($candidates as $candidate) {
            $score = $queryVector->similarity($candidate['vector'], $this->similarityAlgorithm);

            // Insert the search result into the priority queue
            $priorityQueue->insert(
                new Document(
                    $candidate['contents'],
                    new Metadata(
                        array_merge($candidate['metadata']->toArray(), ['score' => $score])
                    )
                ),
                -$score // Use negative score as priority to maintain a max-heap
            );

            // Remove the lowest scoring vector from the priority queue if it exceeds the maximum number of results
            if ($priorityQueue->count() > $numResults) {
                $priorityQueue->extract();
            }
        }

        // Generate the search results.
        $results = [];
        while ( ! $priorityQueue->isEmpty()) {
            array_unshift($results, $priorityQueue->extract());
        }

        return new Documents($results);
    }

    private function generateHyperplanes(): VectorsContract
    {
        $hyperplanes = new Vectors();
        for ($i = 0; $i < $this->numHyperplanes; $i++) {
            $hyperplane = [];
            for ($j = 0; $j < $this->numDimensions; $j++) {
                $hyperplane[] = ($this->randomDimensionGenerator)();
            }
            $hyperplanes[] = new Vector($hyperplane);
        }

        return $hyperplanes;
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
