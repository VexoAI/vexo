# WebText Chain

The WebText chain downloads a webpage and extracts plain text from it. This is useful, for instance, when we have a URL which needs to be inspected, and a subsequent language model chain will inspect or summarize the contents.

You can use it as follows.

```php
use Vexo\Chain\Input;
use Vexo\Chain\WebTextChain;

$chain = new WebTextChain();

// Call the chain
$output = $chain->process(
    new Input(['url' => 'https://example.com'])
);

// Outputs something like:
// Example Domain This domain is for use in illustrative...
echo $output['text'];
```

## Customizing Input and Output

You can change the input and output keys that the chain uses by providing them as constructor aguments.

```php
$chain = new WebTextChain(
    inputKey: 'location',
    outputKey: 'contents'
);
```

## Changing the text limit

By default the chain limits its output to 8000 bytes. You can change that by providing the `maxTextLength` argument.

```php
$chain = new WebTextChain(
    maxTextLength: 4000
);
```

## Changing the HTTP Client

WebTextChain uses `php-http/discovery` to automatically detect and use an available [PSR-18](https://www.php-fig.org/psr/psr-18/) compatible client and [PSR-17](https://www.php-fig.org/psr/psr-17/) compatible request factory. If you would like to inject your own HTTP client or request factory, you can do so through the `httpClient` and `requestFactory` arguments.

```php
$chain = new WebTextChain(
    httpClient: new GuzzleHttp\Client(),
    requestFactory: new GuzzleHttp\Psr7\HttpFactory()
);
```

## Changing the text extractor

By default WebTextChain uses a `DOMDocument` based text extractor based on which strips a downloaded webpage from all its HTML and superfluous whitespace. If you would like to have WebTextChain use your own text extractor, you can simply implement the `Vexo\Chain\WebTextChain\TextExtractor` interface and provide an instance of it through the `textExtractor` argument.

```php

use Vexo\Chain\WebTextChain\TextExtractor;

$textExtractor = new class() implements TextExtractor {
    public function extract(string $contents): string {
        return strip_tags($contents);
    }
};

$chain = new WebTextChain(
    textExtractor: $textExtractor
);
```
