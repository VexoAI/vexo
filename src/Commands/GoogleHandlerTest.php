<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as HttpResponse;
use PHPUnit\Framework\TestCase;

final class GoogleHandlerTest extends TestCase
{
    private GoogleHandler $handler;
    private HttpClient $httpClient;
    private MockHandler $httpClientMockHandler;

    protected function setUp(): void
    {
        $this->httpClientMockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->httpClientMockHandler);
        $this->httpClient = new HttpClient(['handler' => $handlerStack]);

        $this->handler = new GoogleHandler(
            $this->httpClient,
            'test_api_key',
            'test_custom_search_engine_id'
        );
    }

    public function testHandlesReturnsCorrectCommandClasses(): void
    {
        $this->assertSame([Google::class], $this->handler->handles());
    }

    public function testHandle(): void
    {
        $query = 'test query';
        $command = new Google($query);

        $responseBody = json_encode([
            'items' => [
                [
                    'title' => 'Test Title',
                    'link' => 'https://www.example.com',
                ],
            ],
        ]);

        $httpResponse = new HttpResponse(200, [], $responseBody);
        $this->httpClientMockHandler->append($httpResponse);

        $result = $this->handler->handle($command);

        $expectedResults = [
            [
                'title' => 'Test Title',
                'link' => 'https://www.example.com',
            ],
        ];

        $this->assertEquals($expectedResults, $result->data);
    }

    public function testHandleInvalidItems(): void
    {
        $query = 'test query';
        $command = new Google($query);

        $responseBody = json_encode([
            'invalid_items' => [],
        ]);

        $httpResponse = new HttpResponse(200, [], $responseBody);
        $this->httpClientMockHandler->append($httpResponse);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or missing "items" in the response.');

        $this->handler->handle($command);
    }

    public function testHandleMissingTitle(): void
    {
        $query = 'test query';
        $command = new Google($query);

        $responseBody = json_encode([
            'items' => [
                [
                    'link' => 'https://www.example.com',
                ],
            ],
        ]);

        $httpResponse = new HttpResponse(200, [], $responseBody);
        $this->httpClientMockHandler->append($httpResponse);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid item structure: Missing "title" or "link".');

        $this->handler->handle($command);
    }

    public function testHandleMissingLink(): void
    {
        $query = 'test query';
        $command = new Google($query);

        $responseBody = json_encode([
            'items' => [
                [
                    'title' => 'Test Title',
                ],
            ],
        ]);

        $httpResponse = new HttpResponse(200, [], $responseBody);
        $this->httpClientMockHandler->append($httpResponse);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid item structure: Missing "title" or "link".');

        $this->handler->handle($command);
    }
}
