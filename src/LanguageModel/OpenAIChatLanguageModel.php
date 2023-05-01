<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use OpenAI\Responses\Chat\CreateResponse;
use Vexo\Prompt\Prompt;

final class OpenAIChatLanguageModel extends BaseLanguageModel
{
    private const DEFAULT_MODEL = 'gpt-3.5-turbo';

    public function __construct(
        private readonly ChatContract $chat,
        private readonly Parameters $defaultParameters = new Parameters()
    ) {
        $this->defaultParameters->putIfAbsent('model', self::DEFAULT_MODEL);
    }

    protected function call(Prompt $prompt, string ...$stops): Response
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
