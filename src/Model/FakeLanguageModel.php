<?php

declare(strict_types=1);

namespace Vexo\Model;

use Assert\Assertion as Ensure;
use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Prompt\Prompt;

final class FakeLanguageModel implements LanguageModel, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function __construct(private array $responses)
    {
        Ensure::allIsInstanceOf($responses, Response::class);
    }

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        Ensure::notEmpty($this->responses, 'No more responses to return');

        return array_shift($this->responses);
    }
}
