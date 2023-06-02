<?php

declare(strict_types=1);

namespace Vexo\Model\Completion;

final class FakeModel implements LanguageModel
{
    /**
     * @var array<Result>
     */
    private array $results = [];

    /**
     * @var array<int, array{prompt: string, stops: array<string>}>>
     */
    private array $calls = [];

    /**
     * @param array<Result> $results
     */
    public function __construct(array $results = [])
    {
        foreach ($results as $result) {
            $this->addResult($result);
        }
    }

    public function addResult(Result $result): void
    {
        $this->results[] = $result;
    }

    /**
     * @return array<int, array{prompt: string, stops: array<string>}>>
     */
    public function calls(): array
    {
        return $this->calls;
    }

    public function generate(string $prompt, array $stops = []): Result
    {
        $this->calls[] = ['prompt' => $prompt, 'stops' => $stops];

        if ($this->results === []) {
            throw new \LogicException('No more results to return.');
        }

        return array_shift($this->results);
    }
}
