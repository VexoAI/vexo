<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Pragmatist\Assistant\Prompt\Prompt;

use Assert\Assertion as Ensure;

final class FakeLLM implements LLM
{
    public function __construct(private array $responses)
    {
        Ensure::allIsInstanceOf($responses, Response::class);
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt ...$prompts): Response
    {
        Ensure::notEmpty($this->responses, 'No more responses to return');

        return array_shift($this->responses);
    }
}