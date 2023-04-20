<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Google\Service\CustomSearchAPI;
use Google\Service\CustomSearchAPI\Search;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GoogleSearchChain::class)]
final class GoogleSearchChainTest extends TestCase
{
    private CustomSearchAPI $service;

    private GoogleSearchChain $googleSearchChain;

    public function setUp(): void
    {
        $this->service = new CustomSearchAPI();
        $this->service->cse = new CseStub();
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

        $input = new Input(['query' => 'Best restaurant in Amsterdam']);
        $output = $this->googleSearchChain->process($input);

        $this->assertEquals(
            ['results' => $this->service->cse->returnItems],
            $output->data()
        );
    }

    public function testInputKeys(): void
    {
        $this->assertSame(['query'], $this->googleSearchChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $this->assertSame(['results'], $this->googleSearchChain->outputKeys());
    }
}

final class CseStub
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
