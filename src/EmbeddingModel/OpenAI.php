<?php

declare(strict_types=1);

namespace Vexo\EmbeddingModel;

use OpenAI\Contracts\Resources\EmbeddingsContract;
use Ramsey\Collection\Map\AssociativeArrayMap;
use Ramsey\Collection\Map\MapInterface;
use Vexo\Contract\Vector\Implementation;
use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;

final class OpenAI implements EmbeddingModel
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

    public function embedQuery(string $query): Vector
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($query)
        );

        return new Implementation\Vector($response->embeddings[0]->embedding);
    }

    /**
     * @param array<string> $texts
     */
    public function embedTexts(array $texts): Vectors
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($texts)
        );

        $embeddings = new Implementation\Vectors();
        foreach ($response->embeddings as $embedding) {
            $embeddings->offsetSet($embedding->index, new Implementation\Vector($embedding->embedding));
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
