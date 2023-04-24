<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Google\Service\CustomSearchAPI;

final class GoogleSearch extends BaseTool
{
    protected string $name = 'Google Search';

    protected string $description = 'A wrapper around Google Search. '
        . 'Useful for when you need to answer questions about current events. '
        . 'Input should be a search query.';

    public function __construct(
        private CustomSearchAPI $search,
        private string $searchEngineId,
    ) {
    }

    protected function call(string $input): string
    {
        $results = $this->search->cse->listCse([
            'cx' => $this->searchEngineId,
            'q' => $input
        ]);

        if (empty($results->getItems())) {
            return 'No good Google Search result was found';
        }

        return array_reduce($results->getItems(), fn ($carry, $result) => $carry . $result->snippet . "\n");
    }
}
