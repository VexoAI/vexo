<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Assert\Assertion as Ensure;
use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Concerns\SupportsLogging;

final class GoogleSearchChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;

    public function __construct(
        private CustomSearchAPI $search,
        private string $searchEngineId
    ) {
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);
        $query = (string) $input->get('query');
        $number = (int) $input->get('number', 3);

        $this->logger()->debug('Performing google search', ['query' => $query, 'number' => $number]);

        $results = $this->search->cse->listCse([
            'cx' => $this->searchEngineId,
            'q' => $query,
            'num' => $number
        ]);

        return new Output([
            'query' => $query,
            'results' => array_map(
                function (Result $result) {
                    return [ 'title' => $result->title, 'link' => $result->link, 'snippet' => $result->snippet];
                },
                $results->getItems()
            )
        ]);
    }

    private function validateInput(Input $input): void
    {
        Ensure::keyExists($input->data(), 'query');
    }
}
