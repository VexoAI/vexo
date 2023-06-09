<?php

declare(strict_types=1);

namespace Vexo\Model\Completion;

use OpenAI\Contracts\Resources\ChatContract;
use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Contract\Event;
use Vexo\Contract\Metadata;

final class OpenAIChatModel implements Model
{
    private const DEFAULT_PARAMETERS = ['model' => 'gpt-3.5-turbo'];

    /**
     * @var array<string, mixed>
     */
    private readonly array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly ChatContract $chat,
        array $parameters = [],
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->parameters = [...self::DEFAULT_PARAMETERS, ...$parameters];
    }

    public function generate(string $prompt, array $stops = []): Result
    {
        try {
            $response = $this->chat->create(
                $this->prepareParameters($prompt, $stops)
            );
        } catch (\Throwable $exception) {
            throw FailedToGenerateResult::because($exception);
        }

        $result = new Result(
            array_map(fn ($choice): string => $choice->message->content, $response->choices),
            new Metadata([...$this->parameters, 'usage' => $response->usage->toArray()])
        );

        $this->emit(new ModelGeneratedResult($prompt, $stops, $result));

        return $result;
    }

    /**
     * @param array<string> $stops
     *
     * @return array<string, mixed>
     */
    private function prepareParameters(string $prompt, array $stops): array
    {
        $messages = [];
        if (\array_key_exists('messages', $this->parameters) && \is_array($this->parameters['messages'])) {
            $messages = $this->parameters['messages'];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        $parameters = [...$this->parameters, ...['messages' => $messages]];

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
