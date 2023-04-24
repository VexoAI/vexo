<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Search;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GoogleSearchResults::class)]
final class GoogleSearchResultsTest extends TestCase
{
    private CustomSearchAPI $service;

    private GoogleSearchResults $googleSearch;

    public function setUp(): void
    {
        $this->service = new CustomSearchAPI();
        $this->service->cse = new class {
            public function __construct(
                public array $returnItems = [],
                public array $passedParams = []
            ) {
            }

            public function listCse(array $params): Search
            {
                $this->passedParams = $params;

                return new Search(['items' => $this->returnItems]);
            }
        };

        $this->googleSearch = new GoogleSearchResults($this->service, 'abcdef1234567890');
    }

    public function testRun(): void
    {
        $this->service->cse->returnItems = [
            [
                'title' => 'Search result 1',
                'link' => 'https://www.example.com/1',
                'snippet' => 'Search snippet 1',
                'something' => 'else'
            ],
            [
                'title' => 'Search result 2',
                'link' => 'https://www.example.com/2',
                'snippet' => 'Search snippet 2',
                'more' => 'stuff'
            ],
        ];

        $output = $this->googleSearch->run('My search query');

        $this->assertEquals(
            '[{"title":"Search result 1","link":"https:\/\/www.example.com\/1","snippet":"Search snippet 1"},{"title":"Search result 2","link":"https:\/\/www.example.com\/2","snippet":"Search snippet 2"}]',
            $output
        );
    }

    public function testRunWithEmptyResults(): void
    {
        $this->service->cse->returnItems = [];

        $output = $this->googleSearch->run('My search query');

        $this->assertEquals('[]', $output);
    }
}
