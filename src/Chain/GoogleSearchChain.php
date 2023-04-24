<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;

final class GoogleSearchChain extends BaseChain
{
    public function __construct(
        private CustomSearchAPI $search,
        private string $searchEngineId,
        private string $inputKey = 'query',
        private string $outputKey = 'results'
    ) {
    }

    public function inputKeys(): array
    {
        return [$this->inputKey];
    }

    public function outputKeys(): array
    {
        return [$this->outputKey];
    }

    protected function call(Input $input): Output
    {
        $query = (string) $input->get($this->inputKey);

        $results = $this->search->cse->listCse([
            'cx' => $this->searchEngineId,
            'q' => $query
        ]);

        return new Output([
            $this->outputKey => array_map(
                function (Result $result) {
                    return [ 'title' => $result->title, 'link' => $result->link, 'snippet' => $result->snippet];
                },
                $results->getItems()
            )
        ]);
    }
}
