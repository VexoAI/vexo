<?php

declare(strict_types=1);

namespace Vexo\LLM;

use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Vexo\Prompt\Prompt;

final class OpenAIChatLLM implements LLM, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    private static array $defaultParameters = ['model' => 'gpt-3.5-turbo'];

    private Parameters $parameters;

    public function __construct(
        private ChatContract $chat,
        Parameters $parameters = new Parameters([])
    ) {
        $this->parameters = $parameters->withDefaults(self::$defaultParameters);
    }

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $this->eventDispatcher()->dispatch(
            (new LLMStartedGeneratingCompletion($prompt, $stops))->for($this)
        );

        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );
        $generations = $this->extractGenerationsFromChatResponse($chatResponse);

        $this->eventDispatcher()->dispatch(
            (new LLMFinishedGeneratingCompletion($prompt, $stops, $generations))->for($this)
        );

        return $this->createResponse(
            $generations,
            $chatResponse->usage->toArray()
        );
    }

    private function prepareParameters(Prompt $prompt, array $stops): array
    {
        $parameters = $this->parameters->toArray();
        $parameters['messages'] = [['role' => 'user', 'content' => $prompt->text()]];

        if ( ! empty($stops)) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }

    private function extractGenerationsFromChatResponse(CreateResponse $response): Generations
    {
        return new Generations(
            array_map(
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
