<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;
use OpenAI\Client as OpenAIClient;
use GuzzleHttp\Client as HttpClient;
use Pragmatist\Assistant\Commands\BrowseWebpage\TextExtractor;

final class BrowseWebpageHandler implements CommandHandler
{
    public function __construct(
        private TextExtractor $textExtractor,
        private HttpClient $httpClient,
        private OpenAIClient $openAIClient,
        private string $model
    ) {
    }

    public function handles(): array
    {
        return [BrowseWebpage::class];
    }

    public function handle(Command $command): CommandResult
    {
        Ensure::isInstanceOf($command, BrowseWebpage::class);

        return new CommandResult(
            [
                $this->analyzeText(
                    $this->textExtractor->extract($this->fetchPage($command->url)),
                    $command->question
                )
            ]
        );
    }

    private function analyzeText(string $text, string $question): string
    {
        $response = $this->openAIClient->chat()->create(
            [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => 'The following text has been extracted from a webpage:'],
                    ['role' => 'user', 'content' => '"""' . $text . '"""'],
                    ['role' => 'user', 'content' => 'Using the above text, please answer the following question: "' . $question . '"'],
                    ['role' => 'user', 'content' => 'Do not provide other information beyond answering the question.'],
                    ['role' => 'user', 'content' => 'If the question cannot be answered, explicitly say so and then summarize the text.']
                ]
            ]
        );

        return $response->choices[0]->message->content;
    }

    private function fetchPage(string $url): string
    {
        return (string) $this->httpClient->get($url)->getBody();
    }
}