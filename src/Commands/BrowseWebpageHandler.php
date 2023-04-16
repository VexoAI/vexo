<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;
use OpenAI\Client as OpenAIClient;
use GuzzleHttp\Client as HttpClient;
use Pragmatist\Assistant\Commands\BrowseWebpage\TextExtractor;
use Pragmatist\Assistant\Commands\BrowseWebpage\TextSplitter;

final class BrowseWebpageHandler implements CommandHandler
{
    public function __construct(
        private TextExtractor $textExtractor,
        private TextSplitter $textSplitter,
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

    private function fetchPage(string $url): string
    {
        return (string) $this->httpClient->get($url)->getBody();
    }

    private function analyzeText(string $text, string $question): string
    {
        $chunks = $this->textSplitter->divideTextIntoChunks($text);
        $chunkSummaries = $this->summarizeChunks($chunks, $question);
        $combinedSummary = implode("\n", $chunkSummaries);

        return $this->summarizeText($combinedSummary, $question);
    }

    private function summarizeChunks(array $chunks, string $question): array
    {
        $chunkSummaries = [];
        foreach ($chunks as $chunk) {
            $chunkSummaries[] = $this->summarizeText($chunk, $question);
        }
        return $chunkSummaries;
    }

    private function summarizeText(string $text, string $question): string
    {
        $response = $this->openAIClient->chat()->create(
            [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => 'The following text has been extracted from a webpage:'],
                    ['role' => 'user', 'content' => '"""' . $text . '"""'],
                    ['role' => 'user', 'content' => 'Using the above text, please answer the following question: "' . $question . '"'],
                    ['role' => 'user', 'content' => 'Summarize the text if the question cannot be answered directly.']
                ]
            ]
        );

        return $response->choices[0]->message->content;
    }
}