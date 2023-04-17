<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Pragmatist\Assistant\Prompt\Prompt;

use Assert\Assertion as Ensure;
use OpenAI\Contracts\Resources\CompletionsContract;

final class OpenAILLM implements LLM
{
    public function __construct(private CompletionsContract $completions)
    {
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt ...$prompt): Response
    {
        Ensure::notEmpty($prompt, 'No prompts to generate a response for');

    }
}