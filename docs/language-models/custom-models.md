# Custom Language Models

The easiest way to create your own models is by extending the `Vexo\LanguageModel\BaseLanguageModel` class. This will implement the `Vexo\LanguageModel\LanguageModel` and `Vexo\Event\EventDispatcherAware` interfaces out of the box, and will make sure that the standard events are emitted. You will only have to implement a `call` method.

You can do as follows.

```php
use Vexo\LanguageModel\BaseLanguageModel;
use Vexo\LanguageModel\Response;
use Vexo\Prompt\Prompt;

final class AcmeLanguageModel extends BaseLanguageModel
{
    public function __construct(private AcmeClient $acmeClient)
    {
    }

    protected function call(Prompt $prompt, string ...$stops): Response
    {
        return Response::fromString(
            (string) $this->acmeClient->complete((string) $prompt)
        );
    }
}
```

Alternatively you can also instantiate and instance of `Vexo\LanguageModel\Response` yourself if you have additional metadata to pass on.

```php
protected function call(Prompt $prompt, string ...$stops): Response
{
    return new Response(
        Completions::fromString((string) $this->acmeClient->complete((string) $prompt)),
        new ResponseMetadata(['tokenUsage' => $this->acmeClient->getTokenUsage()])
    );
}
```

## Emitting Events

The `BaseLanguageModel` class will emit the `StartedGeneratingCompletion` and `FinishedGeneratingCompletion` events on its own. If you would like to additionally emit your own events, you can use the `emit` method. Make sure your event implements `Vexo\Event\SomethingHappened`.

```php
protected function call(Prompt $prompt, string ...$stops): Response
{
    // Some code...

    $this->emit(new AcmeEvent($prompt, $stops, 'Some other info'));

    // More code here...
}
```
