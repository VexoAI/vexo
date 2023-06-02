<?php

declare(strict_types=1);

namespace Vexo\Model\Completion;

use OpenAI\Contracts\Resources\CompletionsContract;
use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Contract\Event;
use Vexo\Contract\Metadata;

final class OpenAIModel implements Model
{
    private const DEFAULT_PARAMETERS = ['model' => 'text-davinci-003'];

    /**
     * @var array<string, mixed>
     */
    private readonly array $parameters;

    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private readonly CompletionsContract $completions,
        array $parameters = [],
        private readonly ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->parameters = [...self::DEFAULT_PARAMETERS, ...$parameters];
    }

    public function generate(string $prompt, array $stops = []): Result
    {
        try {
            $response = $this->completions->create(
                $this->prepareParameters($prompt, $stops)
            );
        } catch (\Throwable $exception) {
            throw FailedToGenerateResult::because($exception);
        }

        $result = new Result(
            array_map(fn ($choice): string => $choice->text, $response->choices),
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
        $parameters = [...$this->parameters, ...['prompt' => $prompt]];

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
