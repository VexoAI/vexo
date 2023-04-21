<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Vexo\Weave\Chain\Validation\SupportsInputValidation;
use Vexo\Weave\Chain\WebTextChain\SorryHttpRequestFailed;
use Vexo\Weave\Chain\WebTextChain\TextExtractor;
use Vexo\Weave\Logging\SupportsLogging;

final class WebTextChain implements Chain
{
    use SupportsLogging;
    use SupportsInputValidation;

    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private TextExtractor $textExtractor,
        private string $inputKey = 'url',
        private string $outputKey = 'text',
        private int $maxTextLength = 8000
    ) {
    }

    public function inputKeys(): array
    {
        return [$this->inputKey];
    }

    public function outputKeys(): array
    {
        return [$this->outputKey];
    }

    public function process(Input $input): Output
    {
        $this->validateInput($input);
        $url = (string) $input->get($this->inputKey);

        $html = $this->fetchHtml($url);
        $text = $this->extractText($html);

        return new Output([$this->outputKey => $text]);
    }

    private function fetchHtml(string $url): string
    {
        $this->logger()->debug('Fetching web page', ['url' => $url]);

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

        if (strlen($text) > $this->maxTextLength) {
            $text = substr($text, 0, $this->maxTextLength);
        }

        return $text;
    }
}
