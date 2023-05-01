<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use Vexo\Event\EventDispatcherAware;
use Vexo\Event\EventDispatcherAwareBehavior;
use Vexo\Prompt\Prompt;

abstract class BaseLanguageModel implements LanguageModel, EventDispatcherAware
{
    use EventDispatcherAwareBehavior;

    public function generate(Prompt $prompt, string ...$stops): Response
    {
        $this->emit(new StartedGeneratingCompletion($prompt, $stops));

        $response = $this->call($prompt, ...$stops);

        $this->emit(new FinishedGeneratingCompletion($prompt, $stops, $response));

        return $response;
    }

    abstract protected function call(Prompt $prompt, string ...$stops): Response;
}
