<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use OpenAI\Contracts\Resources\ChatContract;
use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Contract\Event\Event;
use Vexo\Contract\Metadata\Implementation\Metadata;

final class OpenAIChatModel implements LanguageModel
{
    private const DEFAULT_PARAMETERS = ['model' => 'gpt-3.5-turbo'];

    private readonly array $parameters;

    public function __construct(
        private readonly ChatContract $chat,
        array $parameters = [],
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->parameters = array_merge(self::DEFAULT_PARAMETERS, $parameters);
    }

    public function generate(string $prompt, array $stops = []): Result
    {
        $chatResponse = $this->chat->create(
            $this->prepareParameters($prompt, $stops)
        );

        $result = new Result(
            array_map(fn ($choice): string => $choice->message->content, $chatResponse->choices),
            new Metadata([...$this->parameters, 'usage' => $chatResponse->usage->toArray()])
        );

        $this->emit(new ModelGeneratedResult($prompt, $stops, $result));

        return $result;
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

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
