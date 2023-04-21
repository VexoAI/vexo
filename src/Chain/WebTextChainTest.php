<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PsrMock\Psr17\RequestFactory;
use PsrMock\Psr18\Client;
use PsrMock\Psr7\Response;
use PsrMock\Psr7\Stream;
use Vexo\Weave\Chain\WebTextChain\SorryHttpRequestFailed;
use Vexo\Weave\Chain\WebTextChain\TextExtractor;

#[CoversClass(WebTextChain::class)]
final class WebTextChainTest extends TestCase
{
    public function testProcess(): void
    {
        $httpClient = new Client();
        $webTextChain = new WebTextChain(
            httpClient: $httpClient,
            requestFactory: new RequestFactory(),
            textExtractor: new TextExtractor(),
            maxTextLength: 27
        );

        $httpClient->addResponse(
            'GET',
            'http://example.com',
            new Response(stream: new Stream('<html><body><p>This is an amazing website! It is great!</p></body></html>'))
        );

        $input = ['url' => 'http://example.com'];
        $output = $webTextChain->process(new Input($input));

        $this->assertEquals(
            ['text' => 'This is an amazing website!'],
            $output->data()
        );
    }

    public function testProcessThrowsCorrectExceptionOnClientException(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory(),
            textExtractor: new TextExtractor()
        );

        $this->expectException(SorryHttpRequestFailed::class);
        $webTextChain->process(new Input(['url' => 'http://example.com']));
    }

    public function testInputKeys(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory(),
            textExtractor: new TextExtractor(),
            inputKey: 'link'
        );

        $this->assertSame(['link'], $webTextChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory(),
            textExtractor: new TextExtractor(),
            outputKey: 'contents'
        );

        $this->assertSame(['contents'], $webTextChain->outputKeys());
    }
}
