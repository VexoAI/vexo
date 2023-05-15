<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use League\Event\EventDispatcher;
use OpenAI\Responses\Completions\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenAIModel::class)]
final class OpenAIModelTest extends TestCase
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
                'id' => 'cmpl-uqkvlQyYK7bGYrRHQ0eXlWi7',
                'object' => 'text_completion',
                'created' => 0,
                'model' => 'text-davinci-003',
                'usage' => [
                    'prompt_tokens' => 15,
                    'completion_tokens' => 8,
                    'total_tokens' => 23
                ],
                'choices' => [
                    ['index' => 0, 'text' => 'Paris', 'logprobs' => null, 'finish_reason' => 'length'],
                    ['index' => 1, 'text' => 'The capital of France is Paris.', 'logprobs' => null, 'finish_reason' => 'length']
                ]
            ])
        ]);

        $model = new OpenAIModel($client->completions(), ['n' => 2], $eventDispatcher);

        $result = $model->generate('What is the capital of France?', ["\n"]);
        $generations = $result->generations();

        $this->assertCount(2, $generations);
        $this->assertEquals('Paris', $generations[0]);
        $this->assertEquals('The capital of France is Paris.', $generations[1]);

        $metadata = $result->metadata();
        $this->assertEquals('text-davinci-003', $metadata->get('model'));
        $this->assertEquals(2, $metadata->get('n'));
        $this->assertEquals(15, $metadata->get('usage')['prompt_tokens']);
        $this->assertEquals(8, $metadata->get('usage')['completion_tokens']);
        $this->assertEquals(23, $metadata->get('usage')['total_tokens']);

        $this->assertCount(1, $emittedEvents);
        $this->assertEquals('What is the capital of France?', $emittedEvents[0]->prompt());
        $this->assertEquals(["\n"], $emittedEvents[0]->stops());
        $this->assertSame($result, $emittedEvents[0]->result());
    }
}
