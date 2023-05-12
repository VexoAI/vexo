<?php

declare(strict_types=1);

namespace Vexo\Chain\WebTextChain;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Vexo\Chain\Attribute\RequiresContextValue;
use Vexo\Chain\Chain;
use Vexo\Chain\Context;

final class WebTextChain implements Chain
{
    public function __construct(
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?TextExtractor $textExtractor = null,
        private readonly int $maxTextLength = 8000
    ) {
        $this->httpClient ??= Psr18ClientDiscovery::find();
        $this->requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        $this->textExtractor ??= new HtmlTextExtractor();
    }

    #[RequiresContextValue('url', 'string')]
    public function run(Context $context): void
    {
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
