<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Assert\Assertion as Ensure;
use Vexo\Weave\Prompt\Prompts;

final class FakeLLM implements LLM
{
    public function __construct(private array $responses)
    {
        Ensure::allIsInstanceOf($responses, Response::class);
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompts $prompts, string ...$stops): Response
    {
        Ensure::notEmpty($this->responses, 'No more responses to return');

        return array_shift($this->responses);
    }
}
