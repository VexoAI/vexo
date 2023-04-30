# Caching Chain

The chain wraps another chain to provide it with caching capabilities. It relies on being provided a [PSR-16](https://www.php-fig.org/psr/psr-16/) compatible cache implementation.

In the example below we wrap it around a WebTextChain to cache webpage text extractions.

```php
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Vexo\Chain\CachingChain;
use Vexo\Chain\Input;
use Vexo\Chain\WebTextChain;

$cachingChain = new CachingChain(
    chain: new WebTextChain(),
    cache: new Psr16Cache(new ArrayAdapter())
);

$output = $cachingChain->process(
    new Input(['url' => 'https://example.com/'])
); // Will trigger an HTTP request

$output = $cachingChain->process(
    new Input(['url' => 'https://example.com/'])
); // Second call is returned from cache
```

## Cache lifetime

By default no lifetime for the cached outputs is specified, which means that the default lifetime set on the injected cache will be used. If you would like CachingChain to use a specific lifetime, you can provide it using the `lifetime` argument.

```php
$cachingChain = new CachingChain(
    chain: new WebTextChain(),
    cache: new Psr16Cache(new ArrayAdapter()),
    lifetime: 300 // Cache for 5 minutes
);
```

## Cache keys

CachingChain bases the cache keys on the class name of the nested chain, and a SHA-256 hash of the input data. In the example above, it would result in using the following cache key:

```
vexo.chain.webtextchain.1fe906a36169711200b28ac6f2c5d4abda77d2d6b58025eb62c1a1de1041a6f9
```

This means that in some scenarios, if the cache is shared between multiple CachingChains each wrapping an instance of the same chain, they may return eachother's cache entries if the input is the same.

You can override the cache key prefix by providing the `cacheKeyPrefix` argument.

```php
$cachingChain = new CachingChain(
    chain: new WebTextChain(),
    cache: new Psr16Cache(new ArrayAdapter()),
    cacheKeyPrefix: 'my.prefix'
);
```

In the example above the cache key used would now be as follows:

```
my.prefix.1fe906a36169711200b28ac6f2c5d4abda77d2d6b58025eb62c1a1de1041a6f9
```
