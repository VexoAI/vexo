<?php

declare(strict_types=1);

namespace Vexo\VectorStore;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Probots\Pinecone\Client;
use Saloon\Contracts\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Vexo\Contract\Metadata\Metadata;
use Vexo\Contract\Vector\Vector;
use Vexo\Contract\Vector\Vectors;
use Vexo\Model\Embedding\FakeModel;

#[CoversClass(PineconeVectorStore::class)]
final class PineconeVectorStoreTest extends TestCase
{
    private MockClient $mockClient;

    private FakeModel $embeddingModel;

    private PineconeVectorStore $vectorStore;

    protected function setUp(): void
    {
        $this->mockClient = new MockClient(
            [
                MockResponse::make([
                    'database' => [
                        'name' => 'test-index',
                        'dimension' => 2,
                        'metric' => 'cosine',
                        'pods' => 1,
                        'replicas' => 1,
                        'shards' => 1,
                        'pod_type' => 'p1.x1'
                    ],
                    'status' => [
                        'ready' => true,
                        'state' => 'Ready',
                        'host' => 'test'
                    ]
                ])
            ]
        );

        $this->embeddingModel = new FakeModel();

        $this->vectorStore = new PineconeVectorStore(
            embeddingModel: $this->embeddingModel,
            pinecone: (new Client(apiKey: '', environment: ''))
                ->withMockClient($this->mockClient)
                ->index('test-index')
                ->vectors(),
            upsertBatchSize: 2
        );
    }

    public function testAddVectors(): void
    {
        $this->mockClient->addResponses([
            function (PendingRequest $request): MockResponse {
                $vectors = $request->getRequest()->body()->get('vectors');

                $this->assertCount(2, $vectors);

                $this->assertEquals(1, $vectors[0]['id']);
                $this->assertEquals('Some contents 1', $vectors[0]['metadata']['contents']);
                $this->assertEquals([-0.86735673894517, 0.77182569530412], $vectors[0]['values']);

                $this->assertEquals(2, $vectors[1]['id']);
                $this->assertEquals('Some contents 2', $vectors[1]['metadata']['contents']);
                $this->assertEquals([0.91102167866706, 0.72931519138129], $vectors[1]['values']);

                return MockResponse::make(['upsertedCount' => 2]);
            },
            function (PendingRequest $request): MockResponse {
                $vectors = $request->getRequest()->body()->get('vectors');

                $this->assertCount(1, $vectors);

                $this->assertEquals(3, $vectors[0]['id']);
                $this->assertEquals('Some contents 3', $vectors[0]['metadata']['contents']);
                $this->assertEquals([0.31876312257664, -0.49097725445916], $vectors[0]['values']);

                return MockResponse::make(['upsertedCount' => 1]);
            }
        ]);

        $this->vectorStore->addVectors(
            new Vectors([
                new Vector([-0.86735673894517, 0.77182569530412]),
                new Vector([0.91102167866706, 0.72931519138129]),
                new Vector([0.31876312257664, -0.49097725445916])
            ]),
            [
                new Metadata(['id' => 1, 'contents' => 'Some contents 1']),
                new Metadata(['id' => 2, 'contents' => 'Some contents 2']),
                new Metadata(['id' => 3, 'contents' => 'Some contents 3'])
            ]
        );

        $this->mockClient->assertSentCount(3);
    }

    public function testAddVectorsWithoutId(): void
    {
        $this->mockClient->addResponses([
            function (PendingRequest $request): MockResponse {
                $vectors = $request->getRequest()->body()->get('vectors');

                $this->assertCount(1, $vectors);

                $this->assertMatchesRegularExpression(
                    '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
                    $vectors[0]['id']
                );
                $this->assertEquals('Some contents 1', $vectors[0]['metadata']['contents']);
                $this->assertEquals([-0.86735673894517, 0.77182569530412], $vectors[0]['values']);

                return MockResponse::make(['upsertedCount' => 1]);
            }
        ]);

        $this->vectorStore->addVectors(
            new Vectors([new Vector([-0.86735673894517, 0.77182569530412])]),
            [new Metadata(['contents' => 'Some contents 1'])]
        );

        $this->mockClient->assertSentCount(2);
    }

    public function testAddVectorsErrorResponseThrowsException(): void
    {
        $this->mockClient->addResponses([
            MockResponse::make(['code' => 0, 'message' => 'Some error'], 400)
        ]);

        $this->expectException(FailedToAddVectors::class);
        $this->vectorStore->addVectors(
            new Vectors([new Vector([-0.86735673894517, 0.77182569530412])]),
            [new Metadata(['contents' => 'Some contents 1'])]
        );
    }

    public function testSimilaritySearch(): void
    {
        $queryVector = new Vector([-0.10722856740804, 0.5842314779685]);
        $this->embeddingModel->addVector($queryVector);

        $this->mockClient->addResponses([
            function (PendingRequest $request) use ($queryVector): MockResponse {
                $body = $request->getRequest()->body();

                $this->assertTrue($body->get('includeMetadata'));
                $this->assertEquals(2, $body->get('topK'));
                $this->assertEquals($queryVector->toArray(), $body->get('vector'));

                return MockResponse::make(
                    [
                        'matches' => [
                            [
                                'id' => '712215cc-ae30-45ad-94ee-2cb51858a95b',
                                'metadata' => ['contents' => 'Some contents 1'],
                                'score' => 1.0
                            ],
                            [
                                'id' => 'id-123',
                                'metadata' => ['contents' => 'Some contents 2', 'id' => 'id-123'],
                                'score' => 0.99
                            ]
                        ]
                    ]
                );
            }
        ]);

        $documents = $this->vectorStore->similaritySearch('My query', 2);

        $this->assertCount(2, $documents);

        $this->assertEquals('Some contents 1', $documents[0]->contents());
        $this->assertEquals('712215cc-ae30-45ad-94ee-2cb51858a95b', $documents[0]->metadata()->get('id'));
        $this->assertEquals(1.0, $documents[0]->metadata()->get('score'));

        $this->assertEquals('Some contents 2', $documents[1]->contents());
        $this->assertEquals('id-123', $documents[1]->metadata()->get('id'));
        $this->assertEquals(0.99, $documents[1]->metadata()->get('score'));
    }

    public function testSimilaritySearchErrorResponseThrowsException(): void
    {
        $this->embeddingModel->addVector(new Vector([-0.10722856740804, 0.5842314779685]));

        $this->mockClient->addResponses([
            MockResponse::make(['code' => 0, 'message' => 'Some error'], 400)
        ]);

        $this->expectException(FailedToPerformSimilaritySearch::class);
        $this->vectorStore->similaritySearch('My query', 2);
    }
}
