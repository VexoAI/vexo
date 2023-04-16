<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Assert\Assertion as Ensure;
use GuzzleHttp\Client as HttpClient;
use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface as HttpResponse;

final class GoogleHandler implements CommandHandler
{
    const BASE_URI = 'https://www.googleapis.com/customsearch/v1?key=%s&cx=%s&q=%s';

    public function __construct(
        private HttpClient $client,
        private string $apiKey,
        private string $customSearchEngineId
    ) {
    }

    public function handles(): array
    {
        return [Google::class];
    }

    public function handle(Command $command): CommandResult
    {
        Ensure::isInstanceOf($command, Google::class);

        return new CommandResult(
            $this->extractResults(
                $this->client->get(
                    sprintf(self::BASE_URI, $this->apiKey, $this->customSearchEngineId, $command->query)
                )
            )
        );
    }

    private function extractResults(HttpResponse $response, int $count = 5): array
    {
        $decoded = Json::decode((string) $response->getBody(), true);

        if (!isset($decoded['items']) || !is_array($decoded['items'])) {
            throw new \InvalidArgumentException('Invalid or missing "items" in the response.');
        }

        return array_map(function ($item) {
            if (!isset($item['title']) || !isset($item['link'])) {
                throw new \InvalidArgumentException('Invalid item structure: Missing "title" or "link".');
            }

            return [
                'title' => $item['title'],
                'link' => $item['link']
            ];
        }, array_slice($decoded['items'], 0, $count));
    }
}