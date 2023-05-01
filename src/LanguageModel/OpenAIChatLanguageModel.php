<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Prompt\Prompt;

final class OpenAIChatLanguageModel implements LanguageModel, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    private const DEFAULT_MODEL = 'gpt-3.5-turbo';

    public function __construct(
        private readonly ChatContract $chat,
        private readonly Parameters $defaultParameters = new Parameters()
    ) {
        $this->defaultParameters->putIfAbsent('model', self::DEFAULT_MODEL);
    }

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $this->emit(new StartedGeneratingCompletion($prompt, $stops));

        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );
        $completions = $this->extractCompletionsFromChatResponse($chatResponse);

        $this->emit(new FinishedGeneratingCompletion($prompt, $stops, $completions));

        return $this->createResponse(
            $completions,
            $chatResponse->usage->toArray()
        );
    }

    private function prepareParameters(Prompt $prompt, array $stops): array
    {
        $parameters = $this->defaultParameters->toArray();
        $parameters['messages'][] = ['role' => 'user', 'content' => $prompt->text()];

        if ($stops !== []) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }

    private function extractCompletionsFromChatResponse(CreateResponse $response): Completions
    {
        return new Completions(
            array_map(
                fn ($choice): \Vexo\LanguageModel\Completion => new Completion($choice->message->content),
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
