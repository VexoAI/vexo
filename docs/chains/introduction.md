# Introduction

Chains are a generic way to combine individual components to enable more complex workflows or use cases that go beyond a single interaction with a model. Sequential chains are the most common which are generally used to have the output from one model or tool feed into the next one while maintaining overall context.

In Vexo, each chain implements the `Vexo\Chain\Chain` interface, which defines three methods.

| Method                          | Description                                                                        |
| ------------------------------- | -----------------------------------------------------------------------------------|
| `inputKeys(): string[]`         | Returns an array of input variables which the chain accepts.                       |
| `outputKeys(): string[]`        | Returns an array of output variables which the chain produces.                     |
| `process(Input $input): Output` | Runs the chain. Takes an instance of `Input` and produces an instance of `Output`. |

This generic interface allows us to easily swap out one chain for another if needed, and enables us to easily validate whether the output from one chain can feed into the next.

## Inputs and Outputs

A chain's process method always takes a single `Input` object containing all the parameters it needs. You can create one as follows:

```php
// Assumes $chain is an instance of Vexo\Chain\Chain
$input = new Input(['query' => 'What is the current weather in Amsterdam?']);
$output = $chain->process($input);
```

Which keys you use in the array are dependent upon the specific chain you feed this input to. All chains will verify that the input contains all the required variables they need to continue. If it does not, the chain will throw a `Vexo\Chain\SorryValidationFailed` exception.

Once the chain has finished processing, it will return an instance of `Vexo\Chain\Output` containing all the output values that the chain has produced. This class implements both PHP's [`ArrayAccess`](https://www.php.net/manual/en/class.arrayaccess.php) and [`IteratorAggregate`](https://www.php.net/manual/en/class.iteratoraggregate) so you can iterate over the values or access them directly.

```php
// Get a single value
$report = $output['report'];

// Iterate over the output
foreach ($output as $name => $value) {
    echo "{$name}: {$value}\n";
}
```
