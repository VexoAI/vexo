<?php

declare(strict_types=1);

namespace Vexo\Weave\LLM;

use Assert\Assertion as Ensure;
use OpenAI\Contracts\Resources\ChatContract;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Concerns\SupportsLogging;
use Vexo\Weave\Prompt\Prompt;

final class OpenAIChatLLM implements LLM, LoggerAwareInterface
{
    use SupportsLogging;

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

            $generation = new Generation(
                $this->chat->create(
                    [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => ['role' => 'user', 'content' => $singlePrompt->text()]
                    ]
                )->choices[0]->message->content
            );

            $this->logger()->debug('Generated response', ['generation' => $generation->text()]);
            $generations[] = $generation;
        }

        return new Response($generations);
    }
}