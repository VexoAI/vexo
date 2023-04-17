<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompt;

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
    public function generate(Prompt ...$prompt): Response
    {
        Ensure::notEmpty($prompt, 'No prompts to generate a response for');
        Ensure::notEmpty($this->responses, 'No more responses to return');

        return array_shift($this->responses);
    }
}