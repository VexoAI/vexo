<?php

declare(strict_types=1);

namespace Vexo\Model\Embedding;

use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Embeddings\CreateResponse;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\ClientFake;
use OpenAI\Testing\Requests\TestRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAIModel::class)]
final class OpenAIModelTest extends TestCase
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

        $model = new OpenAIModel($client->embeddings());
        $vector = $model->embedQuery('What was the food like?');

        $this->assertEquals([0.01, -0.03, 0.04, -0.01], $vector->toArray());
    }

    public function testEmbedQueryThrowsException(): void
    {
        $client = new class() extends ClientFake {
            public function record(TestRequest $request): ResponseContract|StreamResponse|string
            {
                throw new \Exception('A terrible error occurred');
            }
        };

        $model = new OpenAIModel($client->embeddings());

        $this->expectException(FailedToEmbedText::class);
        $model->embedQuery('What was the food like?');
    }

    public function testEmbedTexts(): void
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

        $model = new OpenAIModel($client->embeddings());
        $vectors = $model->embedTexts([
            'The food was amazing and delicious.',
            'The service was slow but the food was great.'
        ]);

        $this->assertEquals([0.01, -0.03, 0.04, -0.01], $vectors->first()->toArray());
        $this->assertEquals([0.02, -0.04, 0.05, -0.02], $vectors->last()->toArray());
    }

    public function testEmbedTextsThrowsException(): void
    {
        $client = new class() extends ClientFake {
            public function record(TestRequest $request): ResponseContract|StreamResponse|string
            {
                throw new \Exception('A terrible error occurred');
            }
        };

        $model = new OpenAIModel($client->embeddings());

        $this->expectException(FailedToEmbedText::class);
        $model->embedTexts([
            'The food was amazing and delicious.',
            'The service was slow but the food was great.'
        ]);
    }
}
