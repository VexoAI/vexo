# Events

Models will emit the events outlined below. For more info on how to listen for events in Vexo, see [Event Handling](../advanced/event-handling.md).

## Emitted events

### `Vexo\Chain\ChainStarted`

Emitted just before the chain will start processing, and before input validation.

| Property                  | Description                                           |
| ------------------------- | ----------------------------------------------------- |
| `Vexo\Chain\Input $input` | The input which was provided to the `process` method. |

### `Vexo\Chain\ChainFinished`

Emitted after the chain has finished processing, just before returning the output.

| Property                    | Description                                           |
| --------------------------- | ----------------------------------------------------- |
| `Vexo\Chain\Input $input`   | The input which was provided to the `process` method. |
| `Vexo\Chain\Output $output` | The output produced by the chain.                     |
