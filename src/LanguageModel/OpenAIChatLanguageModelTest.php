<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Prompt\Prompt;

#[CoversClass(OpenAIChatLanguageModel::class)]
final class OpenAIChatLanguageModelTest extends TestCase
{
    public function testGenerate(): void
    {
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

        $openAIChatLLM = new OpenAIChatLanguageModel($client->chat(), new Parameters(['n' => 2]));

        $response = $openAIChatLLM->generate(new Prompt('What is the capital of France?'), "\n");
        $completions = $response->completions();

        $this->assertCount(2, $completions);
        $this->assertEquals('Paris', $completions[0]->text());
        $this->assertEquals('The capital of France is Paris.', $completions[1]->text());

        $metadata = $response->metadata()->toArray();
        $this->assertEquals('gpt-3.5-turbo', $metadata['model']);
        $this->assertEquals(2, $metadata['n']);
        $this->assertEquals(15, $metadata['usage']['prompt_tokens']);
        $this->assertEquals(8, $metadata['usage']['completion_tokens']);
        $this->assertEquals(23, $metadata['usage']['total_tokens']);
    }
}
