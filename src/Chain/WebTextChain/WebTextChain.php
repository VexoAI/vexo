<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;

final class WebTextChain implements Chain
{
    private readonly ClientInterface $httpClient;

    private readonly RequestFactoryInterface $requestFactory;

    public function __construct(
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        private readonly TextExtractor $textExtractor = new HtmlTextExtractor(),
        private readonly int $maxTextLength = 8000
    ) {
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    public function run(Context $context): void
    {
        /** @var string $url */
        $url = $context->get('url');

        $html = $this->fetchHtml($url);
        $text = $this->extractText($html);

        $context->put('text', $text);
    }

    private function fetchHtml(string $url): string
    {
        try {
            $response = $this->httpClient->sendRequest(
                $this->requestFactory->createRequest('GET', $url)
            );
        } catch (ClientExceptionInterface $e) {
            throw new FailedToFetchHtml($e->getMessage(), $e->getCode(), $e);
        }

        return (string) $response->getBody();
    }

    private function extractText(string $html): string
    {
        $text = $this->textExtractor->extract($html);

        if (\strlen($text) > $this->maxTextLength) {
            return substr($text, 0, $this->maxTextLength);
        }

        return $text;
    }
}
