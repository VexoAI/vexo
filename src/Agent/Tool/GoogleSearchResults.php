<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;

final class GoogleSearchResults implements Tool
{
    private const DEFAULT_NAME = 'Google Search Results JSON';

    private const DEFAULT_DESCRIPTION = 'A wrapper around Google Search. '
        . 'Useful for when you need to answer questions about current events. '
        . 'Input should be a search query. Output is a JSON array of the query results.';

    public function __construct(
        private readonly CustomSearchAPI $search,
        private readonly string $searchEngineId,
        private readonly string $name = self::DEFAULT_NAME,
        private readonly string $description = self::DEFAULT_DESCRIPTION
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function run(string $input): string
    {
        $results = $this->search->cse->listCse([
            'cx' => $this->searchEngineId,
            'q' => $input
        ]);

        if (empty($results->getItems())) {
            return '[]';
        }

        return json_encode(
            array_map(
                fn (Result $result): array => ['title' => $result->getTitle(), 'link' => $result->getLink(), 'snippet' => $result->getSnippet()],
                $results->getItems()
            ),
            \JSON_THROW_ON_ERROR
        );
    }
}
