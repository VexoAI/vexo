<?php

declare(strict_types=1);

namespace Vexo\Embedding;

use OpenAI\Contracts\Resources\EmbeddingsContract;
use Ramsey\Collection\Map\AssociativeArrayMap;
use Ramsey\Collection\Map\MapInterface;

final class OpenAIEmbeddingsModel implements EmbeddingsModel
{
    private const DEFAULT_PARAMETERS = ['model' => 'text-embedding-ada-002'];

    public function __construct(
        private readonly EmbeddingsContract $embeddings,
        private readonly MapInterface $defaultParameters = new AssociativeArrayMap()
    ) {
        foreach (self::DEFAULT_PARAMETERS as $key => $value) {
            $this->defaultParameters->putIfAbsent($key, $value);
        }
    }

    public function embedQuery(string $query): Embedding
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($query)
        );

        return new Embedding($response->embeddings[0]->embedding);
    }

    /**
     * @param array<string> $documents
     */
    public function embedDocuments(array $documents): Embeddings
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($documents)
        );

        $embeddings = new Embeddings();
        foreach ($response->embeddings as $embedding) {
            $embeddings->offsetSet($embedding->index, new Embedding($embedding->embedding));
        }

        return $embeddings;
    }

    /**
     * @param string|array<string> $input
     */
    private function prepareParameters(string|array $input): array
    {
        $parameters = $this->defaultParameters->toArray();
        $parameters['input'] = $input;

        return $parameters;
    }
}
