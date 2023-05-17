<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PsrMock\Psr17\RequestFactory;
use PsrMock\Psr18\Client;
use PsrMock\Psr7\Response;
use PsrMock\Psr7\Stream;
use Vexo\Chain\Context;
use Vexo\Chain\FailedToValidateContextValue;

#[CoversClass(WebTextChain::class)]
final class WebTextChainTest extends TestCase
{
    public function testRun(): void
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

        $context = new Context(['url' => 'http://example.com']);
        $webTextChain->run($context);

        $this->assertEquals('This is an amazing website! It is great!', $context->get('text'));
    }

    public function testRunLimitsMaxTextLength(): void
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

        $context = new Context(['url' => 'http://example.com']);
        $webTextChain->run($context);

        $this->assertEquals('This is an amazing website!', $context->get('text'));
    }

    public function testRunThrowsCorrectExceptionOnClientException(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory()
        );

        $this->expectException(FailedToFetchHtml::class);
        $webTextChain->run(new Context(['url' => 'http://example.com']));
    }

    public function testRunWithInvalidContext(): void
    {
        $webTextChain = new WebTextChain(
            httpClient: new Client(),
            requestFactory: new RequestFactory()
        );

        $this->expectException(FailedToValidateContextValue::class);
        $webTextChain->run(new Context(['url' => '']));
    }
}
