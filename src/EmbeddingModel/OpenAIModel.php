<?php

declare(strict_types=1);

namespace Vexo\EmbeddingModel;

use OpenAI\Contracts\Resources\EmbeddingsContract;
use Vexo\Contract\Vector\Implementation\Vector;
use Vexo\Contract\Vector\Implementation\Vectors;
use Vexo\Contract\Vector\Vector as VectorContract;
use Vexo\Contract\Vector\Vectors as VectorsContract;

final class OpenAIModel implements EmbeddingModel
{
    private const DEFAULT_PARAMETERS = ['model' => 'text-embedding-ada-002'];

    /**
     * @var array<string, mixed>
     */
    private readonly array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly EmbeddingsContract $embeddings,
        array $parameters = []
    ) {
        $this->parameters = [...self::DEFAULT_PARAMETERS, ...$parameters];
    }

    public function embedQuery(string $query): VectorContract
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($query)
        );

        return new Vector($response->embeddings[0]->embedding);
    }

    /**
     * @param array<string> $texts
     */
    public function embedTexts(array $texts): VectorsContract
    {
        $response = $this->embeddings->create(
            $this->prepareParameters($texts)
        );

        $embeddings = new Vectors();
        foreach ($response->embeddings as $embedding) {
            $embeddings->offsetSet($embedding->index, new Vector($embedding->embedding));
        }

        return $embeddings;
    }

    /**
     * @param string|array<string> $input
     *
     * @return array<string, mixed>
     */
    private function prepareParameters(string|array $input): array
    {
        $parameters = $this->parameters;
        $parameters['input'] = $input;

        return $parameters;
    }
}
