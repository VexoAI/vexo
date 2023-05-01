<?php

declare(strict_types=1);

namespace Vexo\Embedding;

use OpenAI\Responses\Embeddings\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAIEmbeddingsModel::class)]
final class OpenAIEmbeddingsModelTest extends TestCase
{
    public function testEmbedQuery(): void
    {
        $client = new ClientFake([
            CreateResponse::from([
                'object' => 'list',
                'model' => 'text-embedding-ada-002',
                'usage' => [
                    'prompt_tokens' => 8,
                    'total_tokens' => 8
                ],
                'data' => [
                    ['object' => 'embedding', 'index' => 0, 'embedding' => [0.01, -0.03, 0.04, -0.01]],
                ]
            ])
        ]);
        $embeddings = $client->embeddings();

        $model = new OpenAIEmbeddingsModel($embeddings);
        $embedding = $model->embedQuery('What was the food like?');

        $this->assertEquals([0.01, -0.03, 0.04, -0.01], $embedding->toArray());
    }

    public function testEmbedDocuments(): void
    {
        $client = new ClientFake([
            CreateResponse::from([
                'object' => 'list',
                'model' => 'text-embedding-ada-002',
                'usage' => [
                    'prompt_tokens' => 8,
                    'total_tokens' => 8
                ],
                'data' => [
                    ['object' => 'embedding', 'index' => 0, 'embedding' => [0.01, -0.03, 0.04, -0.01]],
                    ['object' => 'embedding', 'index' => 1, 'embedding' => [0.02, -0.04, 0.05, -0.02]]
                ]
            ])
        ]);
        $embeddings = $client->embeddings();

        $model = new OpenAIEmbeddingsModel($embeddings);
        $embeddings = $model->embedDocuments([
            'The food was amazing and delicious.',
            'The service was slow but the food was great.'
        ]);

        $this->assertEquals([0.01, -0.03, 0.04, -0.01], $embeddings->first()->toArray());
        $this->assertEquals([0.02, -0.04, 0.05, -0.02], $embeddings->last()->toArray());
    }
}
