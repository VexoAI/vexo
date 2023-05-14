<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use Vexo\Contract\Metadata\Implementation\Metadata;

final class OpenAIChatModel implements LanguageModel
{
    private const DEFAULT_PARAMETERS = ['model' => 'gpt-3.5-turbo'];

    private readonly array $parameters;

    public function __construct(
        private readonly ChatContract $chat,
        array $parameters = []
    ) {
        $this->parameters = array_merge(self::DEFAULT_PARAMETERS, $parameters);
    }

    public function generate(string $prompt, array $stops = []): Result
    {
        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );

        return new Result(
            array_map(fn ($choice): string => $choice->message->content, $chatResponse->choices),
            new Metadata([...$this->parameters, 'usage' => $chatResponse->usage->toArray()])
        );
    }

    private function prepareParameters(string $prompt, array $stops): array
    {
        $parameters = $this->parameters;
        $parameters['messages'][] = ['role' => 'user', 'content' => $prompt];

        if ($stops !== []) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }
}
