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
        $cacheKey = $this->cacheKeyFromPrompts($prompts);

        $response = $this->cache()->get($cacheKey);
        if (null === $response) {
            $response = $this->generateCompletions($prompts, $stops);
            $this->cache()->set($cacheKey, $response);
        }

        return $response;
    }

    private function generateCompletions(Prompts $prompts, array $stops): Response
    {
        $generations = new Generations();
        $tokenUsage = [];

        foreach ($prompts as $prompt) {
            $this->logger()->info('Generating completions for prompt', ['prompt' => $prompt->text()]);

            $parameters = $this->buildCreateParameters($prompt, $stops);
            $result = $this->chat->create($parameters);

            foreach ($result->choices as $choice) {
                $generations[] = new Generation($choice->message->content);
            }

            $tokenUsage = $result->usage->toArray();
        }

        return new Response(
            $generations,
            new ResponseMetadata(array_merge($this->parameters->toArray(), ['usage' => $tokenUsage]))
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

    private function cacheKeyFromPrompts(Prompts $prompts): string
    {
        return hash(
            'sha256',
            array_reduce(
                $prompts->toArray(),
                fn (string $carry, Prompt $prompt): string => $carry . $prompt->text(),
                ''
            )
        );
    }
}