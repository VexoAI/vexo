<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PsrMock\Psr17\RequestFactory;
use PsrMock\Psr18\Client;
use PsrMock\Psr7\Response;
use PsrMock\Psr7\Stream;
use Vexo\Chain\Input;

#[CoversClass(WebTextChain::class)]
final class WebTextChainTest extends TestCase
{
    public function testProcess(): void
    {
        $httpClient = new Client();
        $webTextChain = new WebTextChain(
            httpClient: $httpClient,
            requestFactory: new RequestFactory()
        );

        $httpClient->addResponse(
            'GET',
            'http://example.com',
            new Response(stream: new Stream('<html><body><p>This is an amazing website! It is great!</p></body></html>'))
        );

        $output = $webTextChain->process(new Input(['url' => 'http://example.com']));

        $this->assertEquals(
            ['text' => 'This is an amazing website! It is great!'],
            $output->toArray()
        );
    }

    public function testProcessLimitsMaxTextLength(): void
    {
        $httpClient = new Client();
        $webTextChain = new WebTextChain(
            httpClient: $httpClient,
            requestFactory: new RequestFactory(),
            maxTextLength: 27
        );

        $httpClient->addResponse(
            'GET',
            'http://example.com',
            new Response(stream: new Stream('<html><body><p>This is an amazing website! It is great!</p></body></html>'))
        );

        $output = $webTextChain->process(new Input(['url' => 'http://example.com']));

        $this->assertEquals(
            ['text' => 'This is an amazing website!'],
            $output->toArray()
        );
    }

    public function testProcessThrowsCorrectExceptionOnClientException(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory()
        );

        $this->expectException(FailedToFetchHtml::class);
        $webTextChain->process(new Input(['url' => 'http://example.com']));
    }

    public function testInputKeys(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory(),
            inputKey: 'link'
        );

        $this->assertSame(['link'], $webTextChain->inputKeys());
    }

    public function testOutputKeys(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory(),
            outputKey: 'contents'
        );

        $this->assertSame(['contents'], $webTextChain->outputKeys());
    }
}
