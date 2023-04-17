<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\LLM;

use Pragmatist\Assistant\Prompt\Prompt;

use Assert\Assertion as Ensure;
use OpenAI\Contracts\Resources\ChatContract;

final class OpenAIChatLLM implements LLM
{
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
            $generations[] = new Generation(
                $this->chat->create(
                    [
                        'model' => 'gpt-3.5-turbo',
                        'messages' => ['role' => 'user', 'content' => $singlePrompt->text()]
                    ]
                )->choices[0]->message->content
            );
        }

        return new Response($generations);
    }
}