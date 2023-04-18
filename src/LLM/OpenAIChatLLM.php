<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Assert\Assertion as Ensure;
use OpenAI\Contracts\Resources\ChatContract;
use Psr\Log\LoggerAwareInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Vexo\Weave\Concerns\CacheAware;
use Vexo\Weave\Concerns\SupportsCaching;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\Prompt\Prompt;

final class OpenAIChatLLM implements LLM, LoggerAwareInterface, CacheAware
{
    use SupportsLogging;
    use SupportsCaching;

    public function __construct(private ChatContract $chat)
    {
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt ...$prompt): Response
    {
        Ensure::notEmpty($prompt, 'No prompts to generate a response for');

        $generations = [];
        foreach ($prompt as $singlePrompt) {
            $this->logger()->debug('Generating response for prompt', ['prompt' => $singlePrompt->text()]);
            $generation = $this->generateOrFromCacheForPrompt($singlePrompt);
            $this->logger()->debug('Generated response', ['generation' => $generation->text()]);
            $generations[] = $generation;
        }

        return new Response($generations);
    }

    private function generateOrFromCacheForPrompt(Prompt $prompt): Generation
    {
        return $this->cached(
            $prompt->text(),
            function() use ($prompt) {
                return $this->generateForPrompt($prompt);
            }
        );
    }

    private function generateForPrompt(Prompt $prompt): Generation
    {
        return new Generation(
            $this->chat->create(
                [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => ['role' => 'user', 'content' => $prompt->text()]
                ]
            )->choices[0]->message->content
        );
    }
}