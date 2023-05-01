<?php

declare(strict_types=1);

namespace Vexo\Model;

use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Prompt\Prompt;

final class FakeLanguageModel implements LanguageModel, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    /**
     * @var Response[]
     */
    private array $responses = [];

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

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        if ($this->responses === []) {
            throw new \LogicException('No more responses to return.');
        }

        return array_shift($this->responses);
    }
}
