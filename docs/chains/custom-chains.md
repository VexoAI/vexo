# Custom Chains

The easiest way to create your own chains is by extending the `Vexo\Chain\BaseChain` class. This will implement the `Vexo\Chain\Chain` and `Vexo\Event\EventDispatcherAware` interfaces out of the box, and will make sure that the standard events are emitted. It will also do basic input validation.

You will have to implement the following methods:

- `inputKeys(): array`: Returns an array of input keys that your chain requires to be provided. Omit optional keys.
- `outputKeys(): array`: Returns an array of output keys which your chain will always produce.
- `call(Input $input): Output`: The actual chain functionality, taking input and producing output.

## Example

```php
use Vexo\Chain\BaseChain;
use Vexo\Chain\Input;
use Vexo\Chain\Output;

final class AcmeChain extends BaseChain
{
    public function inputKeys(): array
    {
        return ['question'];
    }

    public function outputKeys(): array
    {
        return ['answer'];
    }

    protected function call(Input $input): Output
    {
        if ($input->get('question') == 'What is the meaning of life?') {
            return new Output(['answer' => 42]);
        }

        return new Output(['answer' => 'Dunno.']);
    }
}

$chain = new AcmeChain();
$output = $chain->process(new Input(['question' => 'What is the meaning of life?']));
echo $output->get('answer'); // Outputs: 42
```

## Emitting Events

The `BaseChain` class will emit the `ChainStarted` and `ChainFinished` events on its own. If you would like to additionally emit your own events, you can use the `emit` method. Make sure your event implements `Vexo\Event\SomethingHappened`.

```php
protected function call(Input $input): Output
{
    // Some code...

    $this->emit(new AcmeEvent($input, 'Some other info'));

    // More code here...
}
```
