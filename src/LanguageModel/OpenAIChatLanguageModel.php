<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Ramsey\Collection\Map\AssociativeArrayMap;
use Ramsey\Collection\Map\MapInterface;
use Vexo\LanguageModel\Prompt\Prompt;

final class OpenAIChatLanguageModel implements LanguageModel
{
    private const DEFAULT_PARAMETERS = ['model' => 'gpt-3.5-turbo'];

    public function __construct(
        private readonly ChatContract $chat,
        private readonly MapInterface $defaultParameters = new AssociativeArrayMap()
    ) {
        foreach (self::DEFAULT_PARAMETERS as $key => $value) {
            $this->defaultParameters->putIfAbsent($key, $value);
        }
    }

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );

        return new Response(
            $this->extractCompletionsFromChatResponse($chatResponse),
            new ResponseMetadata([...$this->defaultParameters->toArray(), 'usage' => $chatResponse->usage->toArray()])
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
                fn ($choice): Completion => new Completion($choice->message->content),
                $response->choices
            )
        );
    }
}
