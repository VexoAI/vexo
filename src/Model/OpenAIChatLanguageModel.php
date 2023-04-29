<?php

declare(strict_types=1);

namespace Vexo\Model;

use League\Event\EventDispatcherAware;
use League\Event\EventDispatcherAwareBehavior;
use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Vexo\Prompt\Prompt;

final class OpenAIChatLanguageModel implements LanguageModel, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    private static string $defaultModel = 'gpt-3.5-turbo';

    public function __construct(
        private ChatContract $chat,
        private Parameters $defaultParameters = new Parameters()
    ) {
        $this->defaultParameters->putIfAbsent('model', self::$defaultModel);
    }

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $this->eventDispatcher()->dispatch(
            (new LanguageModelStartedGeneratingCompletion($prompt, $stops))->for($this)
        );

        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );
        $completions = $this->extractCompletionsFromChatResponse($chatResponse);

        $this->eventDispatcher()->dispatch(
            (new LanguageModelFinishedGeneratingCompletion($prompt, $stops, $completions))->for($this)
        );

        return $this->createResponse(
            $completions,
            $chatResponse->usage->toArray()
        );
    }

    private function prepareParameters(Prompt $prompt, array $stops): array
    {
        $parameters = $this->defaultParameters->toArray();
        $parameters['messages'][] = ['role' => 'user', 'content' => $prompt->text()];

        if ( ! empty($stops)) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }

    private function extractCompletionsFromChatResponse(CreateResponse $response): Completions
    {
        return new Completions(
            array_map(
                fn ($choice) => new Completion($choice->message->content),
                $response->choices
            )
        );
    }

    private function createResponse(Completions $completions, array $tokenUsage): Response
    {
        return new Response(
            $completions,
            new ResponseMetadata(array_merge($this->defaultParameters->toArray(), ['usage' => $tokenUsage]))
        );
    }
}
