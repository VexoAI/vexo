<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Prompt\Prompt;

final class FakeLanguageModel extends BaseLanguageModel
{
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

    protected function call(Prompt $prompt, string ...$stops): Response
    {
        if ($this->responses === []) {
            throw new \LogicException('No more responses to return.');
        }

        return array_shift($this->responses);
    }
}
