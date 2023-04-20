<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;
use Psr\Log\LoggerAwareInterface;
use Vexo\Weave\Chain\Validation\SupportsInputValidation;
use Vexo\Weave\Logging\SupportsLogging;

final class GoogleSearchChain implements Chain, LoggerAwareInterface
{
    use SupportsLogging;
    use SupportsInputValidation;

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

    public function process(Input $input): Output
    {
        $this->validateInput($input);
        $query = (string) $input->get($this->inputKey);

        $this->logger()->debug('Performing google search', ['query' => $query]);

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
