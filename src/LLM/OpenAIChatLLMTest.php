<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use Pragmatist\Assistant\Prompt\Prompt;
use PHPUnit\Framework\TestCase;

final class OpenAIChatLLMTest extends TestCase
{
    public function testGenerate(): void
    {
        $prompt = new Prompt('What is the capital of France?');
        $generatedResponse = 'The capital of France is Paris.';

        $client = new ClientFake([
            CreateResponse::fake([
                'choices' => [['message' => ['role' => 'assistant', 'content' => $generatedResponse]]]
            ])
        ]);

        $openAIChatLLM = new OpenAIChatLLM($client->chat());

        $response = $openAIChatLLM->generate($prompt);
        $generations = $response->generations();

        $this->assertCount(1, $generations);
        $this->assertEquals($generatedResponse, $generations[0]->text());
    }

    public function testGenerateEmptyPromptsThrowsException(): void
    {
        $client = new ClientFake([]);

        $this->expectException(\InvalidArgumentException::class);

        $openAIChatLLM = new OpenAIChatLLM($client->chat());
        $openAIChatLLM->generate();
    }
}
