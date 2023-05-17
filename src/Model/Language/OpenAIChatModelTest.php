<?php

declare(strict_types=1);

namespace Vexo\Model\Language;

use League\Event\EventDispatcher;
use OpenAI\Contracts\ResponseContract;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Responses\StreamResponse;
use OpenAI\Testing\ClientFake;
use OpenAI\Testing\Requests\TestRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAIChatModel::class)]
final class OpenAIChatModelTest extends TestCase
{
    public function testGenerate(): void
    {
        $emittedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            ModelGeneratedResult::class,
            function (ModelGeneratedResult $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );

        $client = new ClientFake([
            CreateResponse::from([
                'id' => 'chatcmpl-555NOEm562iYTOet9ql555znLFWES',
                'object' => 'chat.completion',
                'created' => 0,
                'model' => 'gpt-3.5-turbo-0301',
                'usage' => [
                    'prompt_tokens' => 15,
                    'completion_tokens' => 8,
                    'total_tokens' => 23
                ],
                'choices' => [
                    ['index' => 0, 'message' => ['role' => 'assistant', 'content' => 'Paris'], 'finish_reason' => 'stop'],
                    ['index' => 1, 'message' => ['role' => 'assistant', 'content' => 'The capital of France is Paris.'], 'finish_reason' => 'stop']
                ]
            ])
        ]);

        $model = new OpenAIChatModel($client->chat(), ['n' => 2], $eventDispatcher);

        $result = $model->generate('What is the capital of France?', ["\n"]);
        $generations = $result->generations();

        $this->assertCount(2, $generations);
        $this->assertEquals('Paris', $generations[0]);
        $this->assertEquals('The capital of France is Paris.', $generations[1]);

        $metadata = $result->metadata();
        $this->assertEquals('gpt-3.5-turbo', $metadata->get('model'));
        $this->assertEquals(2, $metadata->get('n'));
        $this->assertEquals(15, $metadata->get('usage')['prompt_tokens']);
        $this->assertEquals(8, $metadata->get('usage')['completion_tokens']);
        $this->assertEquals(23, $metadata->get('usage')['total_tokens']);

        $this->assertCount(1, $emittedEvents);
        $this->assertEquals('What is the capital of France?', $emittedEvents[0]->prompt());
        $this->assertEquals(["\n"], $emittedEvents[0]->stops());
        $this->assertSame($result, $emittedEvents[0]->result());
    }

    public function testGenerateThrowsAppropriateException(): void
    {
        $client = new class() extends ClientFake {
            public function record(TestRequest $request): ResponseContract|StreamResponse|string
            {
                throw new \Exception('A terrible error occurred');
            }
        };

        $model = new OpenAIChatModel($client->chat());

        $this->expectException(FailedToGenerateResult::class);
        $model->generate('What is the capital of France?');
    }
}
