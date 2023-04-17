<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Vexo\Weave\Prompt\Prompt;

use Assert\Assertion as Ensure;
use OpenAI\Contracts\Resources\ChatContract;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

final class OpenAIChatLLM implements LLM, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(private ChatContract $chat)
    {
        $this->logger = new NullLogger();
    }

    /**
     * @inheritDoc
     */
    public function generate(Prompt ...$prompt): Response
    {
        Ensure::notEmpty($prompt, 'No prompts to generate a response for');

        $generations = [];
        foreach ($prompt as $singlePrompt) {
            $this->logger->debug('Generating response for prompt', ['prompt' => $singlePrompt->text()]);

            $generation = new Generation(
                $this->chat->create(
                    [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => ['role' => 'user', 'content' => $singlePrompt->text()]
                    ]
                )->choices[0]->message->content
            );

            $this->logger->debug('Generated response', ['generation' => $generation->text()]);
            $generations[] = $generation;
        }

        return new Response($generations);
    }
}