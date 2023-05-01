<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;

final class GoogleSearchResults extends BaseTool
{
    protected string $name = 'Google Search Results JSON';

    protected string $description = 'A wrapper around Google Search. '
        . 'Useful for when you need to answer questions about current events. '
        . 'Input should be a search query. Output is a JSON array of the query results.';

    public function __construct(
        private readonly CustomSearchAPI $search,
        private readonly string $searchEngineId,
    ) {
    }

    protected function call(string $input): string
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
