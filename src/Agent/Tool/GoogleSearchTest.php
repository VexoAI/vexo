<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Search;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GoogleSearch::class)]
final class GoogleSearchTest extends TestCase
{
    private CustomSearchAPI $service;

    private GoogleSearch $googleSearch;

    protected function setUp(): void
    {
        $this->service = new CustomSearchAPI();
        $this->service->cse = new class() {
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

        $this->googleSearch = new GoogleSearch($this->service, 'abcdef1234567890');
    }

    public function testName(): void
    {
        $this->assertStringContainsString('google', strtolower($this->googleSearch->name()));
    }

    public function testDescription(): void
    {
        $this->assertStringContainsString('wrapper around google', strtolower($this->googleSearch->description()));
    }

    public function testRun(): void
    {
        $this->service->cse->returnItems = [
            [
                'title' => 'Result 1',
                'link' => 'https://example.com/1',
                'snippet' => 'Snippet 1'
            ],
            [
                'title' => 'Result 2',
                'link' => 'https://example.com/2',
                'snippet' => 'Snippet 2'
            ],
        ];

        $output = $this->googleSearch->run('My search query');

        $this->assertEquals(
            "Result 1: Snippet 1 - https://example.com/1\nResult 2: Snippet 2 - https://example.com/2\n",
            $output
        );
    }

    public function testRunWithEmptyResults(): void
    {
        $this->service->cse->returnItems = [];

        $output = $this->googleSearch->run('My search query');

        $this->assertEquals('No good Google Search result was found', $output);
    }
}
