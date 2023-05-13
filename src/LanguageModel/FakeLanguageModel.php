<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Chain\LanguageModelChain\Prompt\Prompt;

final class FakeLanguageModel implements LanguageModel
{
    /**
     * @var Response[]
     */
    private array $responses = [];

    /**
     * @var array<int, array{prompt: string, stops: array<string>}>>
     */
    private array $calls = [];

    public function __construct(array $responses = [])
    {
        foreach ($responses as $response) {
            $this->addResponse($response);
        }
    }

    public function addResponse(Response $response): void
    {
        $this->responses[] = $response;
    }

    public function calls(): array
    {
        return $this->calls;
    }

    public function generate(string $prompt, string ...$stops): Response
    {
        $this->calls[] = ['prompt' => $prompt, 'stops' => $stops];

        if ($this->responses === []) {
            throw new \LogicException('No more responses to return.');
        }

        return array_shift($this->responses);
    }
}
