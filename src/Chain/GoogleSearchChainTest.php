<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Search;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class GoogleSearchChainTest extends TestCase
{
    private CustomSearchAPI $service;

    private GoogleSearchChain $googleSearchChain;

    public function setUp(): void
    {
        $this->service = new CustomSearchAPI();
        $this->service->cse = new TestCse();
        $this->googleSearchChain = new GoogleSearchChain($this->service, 'abcdef1234567890');
    }

    public function testProcess(): void
    {
        $this->service->cse->returnItems = [
            [
                'title' => '20 Best Restaurants in Amsterdam | Condé Nast Traveler',
                'link' => 'https://www.cntraveler.com/gallery/best-restaurants-in-amsterdam',
                'snippet' => 'Dec 3, 2020 ... 20 Best Restaurants in Amsterdam · Balthazars Keuken Restaurant · Daalder ...'
            ]
        ];

        $input = new Input(['query' => 'Best restaurant in Amsterdam', 'number' => 1]);
        $output = $this->googleSearchChain->process($input);

        $this->assertEquals(
            ['query' => 'Best restaurant in Amsterdam', 'results' => $this->service->cse->returnItems],
            $output->data()
        );
    }

    public function testProcessWithMissingQuery(): void
    {
        $input = new Input(['number' => 1]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[query]: This field is missing.');
        $this->googleSearchChain->process($input);
    }

    public function testProcessWithInvalidNumber(): void
    {
        $input = new Input(['query' => 'Best restaurant in Amsterdam', 'number' => 11]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[number]: This value should be between 1 and 10.');
        $this->googleSearchChain->process($input);
    }

    public function testProcessWithNumberBelowRange(): void
    {
        $input = new Input(['query' => 'Best restaurant in Amsterdam', 'number' => 0]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('[number]: This value should be between 1 and 10.');
        $this->googleSearchChain->process($input);
    }
}

final class TestCse
{
    public function __construct(public array $returnItems = [], public array $passedParams = [])
    {
    }

    public function listCse(array $params): Search
    {
        $this->passedParams = $params;

        return new Search(['items' => $this->returnItems]);
    }
}
