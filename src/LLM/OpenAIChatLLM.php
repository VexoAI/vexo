<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use OpenAI\Contracts\Resources\ChatContract;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Concerns\CacheAware;
use Vexo\Weave\Concerns\SupportsCaching;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\Prompt\Prompt;
use Vexo\Weave\Prompt\Prompts;

final class OpenAIChatLLM implements LLM, LoggerAwareInterface, CacheAware
{
    use SupportsLogging;
    use SupportsCaching;

    private static array $defaultParameters = ['model' => 'gpt-3.5-turbo'];

    private Parameters $parameters;

    public function __construct(
        private ChatContract $chat,
        Parameters $parameters = new Parameters([])
    ) {
        $this->parameters = $parameters->withDefaults(self::$defaultParameters);
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompts $prompts, string ...$stops): Response
    {
        $generations = [];
        foreach ($prompts as $prompt) {
            $this->logger()->debug('Generating response for prompt', ['prompt' => $prompt->text()]);
            $generation = $this->generateOrFromCacheForPrompt($prompt, $stops);
            $this->logger()->debug('Generated response', ['generation' => $generation->text()]);
            $generations[] = $generation;
        }

        return new Response($generations);
    }

    private function generateOrFromCacheForPrompt(Prompt $prompt, array $stops): Generation
    {
        return $this->cached(
            $prompt->text(),
            fn () => $this->generateForPrompt($prompt, $stops)
        );
    }

    private function generateForPrompt(Prompt $prompt, array $stops): Generation
    {
        return new Generation(
            $this->chat->create($this->buildCreateParameters($prompt, $stops))
                ->choices[0]->message->content
        );
    }

    private function buildCreateParameters(Prompt $prompt, array $stops): array
    {
        return array_merge_recursive(
            $this->parameters->toArray(),
            [
                'messages' => ['role' => 'user', 'content' => $prompt->text()],
                'stop' => $stops,
            ]
        );
    }
}
