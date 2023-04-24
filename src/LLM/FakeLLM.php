<?php

declare(strict_types=1);

namespace Vexo\LLM;

use Assert\Assertion as Ensure;
use Vexo\Prompt\Prompt;

final class FakeLLM implements LLM
{
    public function __construct(private array $responses)
    {
        Ensure::allIsInstanceOf($responses, Response::class);
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt $prompt, string ...$stops): Response
    {
        Ensure::notEmpty($this->responses, 'No more responses to return');

        return array_shift($this->responses);
    }
}
