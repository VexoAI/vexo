# Event Handling

Multiple components in Vexo emit events through an event dispatcher. Classes which support this all implement the `Vexo\Event\EventDispatcherAware` interface. Right now the following components emit events:

* Agents
* Chains
* Models

By default a Vexo relies on [league/event](https://event.thephpleague.com/) for the default event dispatcher. You can retrieve the event dispatcher by calling the `eventDispatcher()` method on any supported component.

```php
use Vexo\Chain\PassthroughChain;
use Vexo\Event\SomethingHappened;

$chain = new PassthroughChain();
$eventDispatcher = $chain->eventDispatcher(); // Instance of League\Event\EventDispatcher
$eventDispatcher->subscribeTo(
    SomethingHappened::class,
    function (SomethingHappened $event): void {
        // Handle the event
    }
)
```

Look at the documentation for the different components to see which events are emitted. If you would like to subscribe to all events, you can listen for `Vexo\Event\SomethingHappened` on which all events are based.

## Override the Event Dispatcher

You can also provide your own [PSR-14](https://www.php-fig.org/psr/psr-14/) compatible event dispatcher to have the component use that instead.

```php
use League\Event\EventDispatcher;

$eventDispatcher = new EventDispatcher();
$chain->useEventDispatcher($eventDispatcher);
```
