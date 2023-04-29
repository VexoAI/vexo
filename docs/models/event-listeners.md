# Event Listeners

Models will emit events before and after they start work on generating completions for a provided prompt. If you would like to subscribe to these events, you can retrieve the event dispatcher from the model by calling the `LanguageModel::eventDispatcher()` method and attach your own listeners.

```php
// Assumes $model is an implementation of Vexo\Model\LanguageModel
$eventDispatcher = $model->eventDispatcher();

// Add our subscriber
$eventDispatcher->subscribeTo(
    Vexo\Model\StartedGeneratingCompletion::class,
    function ($event): void {
        // Do something with the event here
        echo (string) $event->prompt;
    }
);
```

The example above adds a listener for the StartedGeneratingCompletion event, but if you simply want to listen to all Vexo events, you can pass `Vexo\Event\SomethingHappened::class` instead.

If you want the model you use your own event dispatcher, you can do so by calling the `LanguageModel::useEventDispatcher()` method and pass in a [PSR-14](https://www.php-fig.org/psr/psr-14/) compatible dispatcher.

```php
// Create our own event dispatcher
$eventDispatcher = new League\Event\EventDispatcher();

// Assumes $model is an implementation of Vexo\Model\LanguageModel
$model->useEventDispatcher($eventDispatcher);
```

Vexo itself depends on [league/event](https://event.thephpleague.com/) for its default event dispatcher. If you're going to use this dispatcher and would like to know more about more advanced use cases, please refer to that library's documentation.

## Emitted events

The following events are emitted by language model implementations. Note that all these events are classes in the `Vexo\Model` namespace. The properties are accessible as public properties on the event object.

### StartedGeneratingCompletion

Emitted just before the model will call upon the provide to generate a completion for a given prompt. Contains the following properties.

| Property                     | Description                                                                  |
| ---------------------------- | ---------------------------------------------------------------------------- |
| `Vexo\Prompt\Prompt $prompt` | The prompt which was passed to the `generate` method.                        |
| `string[] $stops`            | An array of stop words provided to the `generate` method, or an empty array. |

### FinishedGeneratingCompletion

Emitted after the model has generated its completion for the given prompt, but before returning it to the caller.

| Property                              | Description                                                                  |
| ------------------------------------- | ---------------------------------------------------------------------------- |
| `Vexo\Prompt\Prompt $prompt`          | The prompt which was passed to the `generate` method.                        |
| `string[] $stops`                     | An array of stop words provided to the `generate` method, or an empty array. |
| `Vexo\Model\Completions $completions` | The completions collection containing the generated completions.             |
