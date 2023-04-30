<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Vexo\Chain\WebTextChain\SorryHttpRequestFailed;
use Vexo\Chain\WebTextChain\TextExtractor;

final class WebTextChain extends BaseChain
{
    public function __construct(
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?TextExtractor $textExtractor = null,
        private string $inputKey = 'url',
        private string $outputKey = 'text',
        private int $maxTextLength = 8000
    ) {
        $this->httpClient ??= Psr18ClientDiscovery::find();
        $this->requestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
        $this->textExtractor ??= new TextExtractor();
    }

    public function inputKeys(): array
    {
        return [$this->inputKey];
    }

    public function outputKeys(): array
    {
        return [$this->outputKey];
    }

    protected function call(Input $input): Output
    {
        $url = (string) $input->get($this->inputKey);

        $html = $this->fetchHtml($url);
        $text = $this->extractText($html);

        return new Output([$this->outputKey => $text]);
    }

    private function fetchHtml(string $url): string
    {
        try {
            $response = $this->httpClient->sendRequest(
                $this->requestFactory->createRequest('GET', $url)
            );
        } catch (ClientExceptionInterface $e) {
            throw new SorryHttpRequestFailed($e->getMessage(), $e->getCode(), $e);
        }

        return (string) $response->getBody();
    }

    private function extractText(string $html): string
    {
        $text = $this->textExtractor->extract($html);

        if (\strlen($text) > $this->maxTextLength) {
            $text = substr($text, 0, $this->maxTextLength);
        }

        return $text;
    }
}
