<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Google\Service\CustomSearchAPI;

final class GoogleSearch implements Tool
{
    private const DEFAULT_NAME = 'Google Search';

    private const DEFAULT_DESCRIPTION = 'A wrapper around Google Search. '
        . 'Useful for when you need to answer questions about current events. '
        . 'Input should be a search query.';

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
            return 'No good Google Search result was found';
        }

        return array_reduce($results->getItems(), fn ($carry, $result): string => $carry . $result->snippet . "\n");
    }
}
