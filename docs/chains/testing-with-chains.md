# Testing Chains

Vexo provides an implementation of `Vexo\LanguageModel\Chain` which you can use for testing classes that depend on it.

This `PassthroughChain` simply outputs whatever its been given as input.

```php
use Vexo\Chain\Input;
use Vexo\Chain\PassthroughChain;

$chain = new PassthroughChain();

// Call the chain
$output = $chain->process(
    new Input(['foo' => 'bar'])
);

// Outputs: bar
echo $output['foo'];
```

If you want the chain to be explicitly aware of the input and output it processes (and validate accordingly), you can provide the keys in the constructor.


```php
$chain = new PassthroughChain(
    inputKeys: ['foo'],
    outputKeys: ['foo']
);
```

If you specifiy the input keys and then pass input to the `process` method that does not contain all of the configured keys, an exception will be thrown.
