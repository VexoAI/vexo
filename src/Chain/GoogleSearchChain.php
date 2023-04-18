<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Result;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Vexo\Weave\Chain\Concerns\SupportsValidation;

final class GoogleSearchChain implements Chain
{
    use SupportsValidation;

    public function __construct(
        private CustomSearchAPI $search,
        private string $searchEngineId
    ) {
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);
        $query = $input->get('query');
        $number = $input->get('number', 3);

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

    private function inputConstraints(): Constraint
    {
        return new Assert\Collection([
            'query' => [
                new Assert\NotBlank()
            ],
            'number' => [
                new Assert\Optional([
                    new Assert\Range(['min' => 1, 'max' => 10])
                ])
            ]
        ]);
    }
}