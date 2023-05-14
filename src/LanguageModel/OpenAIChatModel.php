<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use Ramsey\Collection\Map\AssociativeArrayMap;
use Ramsey\Collection\Map\MapInterface;
use Vexo\Contract\Metadata\Implementation\Metadata;

final class OpenAIChatModel implements LanguageModel
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

    public function generate(string $prompt, array $stops = []): Result
    {
        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );

        return new Result(
            array_map(fn ($choice): string => $choice->message->content, $chatResponse->choices),
            new Metadata([...$this->defaultParameters->toArray(), 'usage' => $chatResponse->usage->toArray()])
        );
    }

    private function prepareParameters(string $prompt, array $stops): array
    {
        $parameters = $this->defaultParameters->toArray();
        $parameters['messages'][] = ['role' => 'user', 'content' => $prompt];

        if ($stops !== []) {
            $parameters['stop'] = $stops;
        }

        return $parameters;
    }
}
