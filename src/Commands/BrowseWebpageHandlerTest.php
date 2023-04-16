<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\TestCase;
use Pragmatist\Assistant\Commands\BrowseWebpage\TextExtractor;
use Pragmatist\Assistant\Commands\BrowseWebpage\TextSplitter;

final class BrowseWebpageHandlerTest extends TestCase
{
    private BrowseWebpageHandler $browseWebpageHandler;
    private HttpClient $httpClient;
    private ClientFake $openAIClient;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $textExtractor = new TextExtractor();
        $textSplitter = new TextSplitter();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new HttpClient(['handler' => $handlerStack]);

        $this->openAIClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'The main topic of the page is the BrowseWebpageHandler class.'
                        ]
                    ]
                ]
            ])
        ]);

        $this->browseWebpageHandler = new BrowseWebpageHandler(
            $textExtractor,
            $textSplitter,
            $this->httpClient,
            $this->openAIClient,
            'test_model'
        );
    }

    public function testHandlesReturnsCorrectCommandClasses(): void
    {
        $this->assertSame([BrowseWebpage::class], $this->browseWebpageHandler->handles());
    }

    public function testHandle(): void
    {
        $url = 'https://example.com';
        $question = 'What is the main topic of the page?';
        $command = new BrowseWebpage($url, $question);

        $html = '<html><body><p>The main topic of the page is the BrowseWebpageHandler class.</p></body></html>';

        $mockResponse = new Response(200, [], $html);
        $this->mockHandler->append($mockResponse);

        $result = $this->browseWebpageHandler->handle($command);

        $this->assertInstanceOf(CommandResult::class, $result);
        $this->assertSame(['The main topic of the page is the BrowseWebpageHandler class.'], $result->data);
    }
}
