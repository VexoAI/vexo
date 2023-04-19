<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\Prompt\Prompts;

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

        $response = $openAIChatLLM->generate(new Prompts($prompt), "\n");
        $generations = $response->generations();

        $this->assertCount(1, $generations);
        $this->assertEquals($generatedResponse, $generations[0]->text());
    }
}
