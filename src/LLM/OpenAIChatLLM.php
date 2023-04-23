<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Logging\SupportsLogging;
use Vexo\Weave\Prompt\Prompt;

final class OpenAIChatLLM implements LLM, LoggerAwareInterface
{
    use SupportsLogging;

    private static array $defaultParameters = ['model' => 'gpt-3.5-turbo'];

    private Parameters $parameters;

    public function __construct(
        private ChatContract $chat,
        Parameters $parameters = new Parameters([])
    ) {
        $this->parameters = $parameters->withDefaults(self::$defaultParameters);
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $this->logger()->debug('Generating completions for prompt', ['prompt' => $prompt, 'stops' => $stops]);

        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );
        $generations = $this->extractGenerationsFromChatResponse($chatResponse);

        $this->logger()->debug('Generation complete', ['generations' => $generations]);

        return $this->createResponse(
            $generations,
            $chatResponse->usage->toArray()
        );
    }

    private function prepareParameters(Prompt $prompt, array $stops): array
    {
        $parameters = $this->parameters->toArray();
        $parameters['messages'] = [['role' => 'user', 'content' => $prompt->text()]];

        if (!empty($stops)) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }

    private function extractGenerationsFromChatResponse(CreateResponse $response): Generations
    {
        return new Generations(
            ...array_map(
                fn ($choice) => new Generation($choice->message->content),
                $response->choices
            )
        );
    }

    private function createResponse(Generations $generations, array $tokenUsage): Response
    {
        return new Response(
            $generations,
            new ResponseMetadata(array_merge($this->parameters->toArray(), ['usage' => $tokenUsage]))
        );
    }
}
