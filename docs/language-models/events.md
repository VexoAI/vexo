# Events

Models will emit the events outlined below. For more info on how to listen for events in Vexo, see [Event Handling](../advanced/event-handling.md).

## Emitted events

### `Vexo\LanguageModel\StartedGeneratingCompletion`

Emitted just before the model will call upon the provide to generate a completion for a given prompt. Contains the following properties.

| Property                     | Description                                                                  |
| ---------------------------- | ---------------------------------------------------------------------------- |
| `Vexo\Prompt\Prompt $prompt` | The prompt which was passed to the `generate` method.                        |
| `string[] $stops`            | An array of stop words provided to the `generate` method, or an empty array. |

### `Vexo\LanguageModel\FinishedGeneratingCompletion`

Emitted after the model has generated its completion for the given prompt, but before returning it to the caller.

| Property                                | Description                                                                     |
| --------------------------------------- | ------------------------------------------------------------------------------- |
| `Vexo\Prompt\Prompt $prompt`            | The prompt which was passed to the `generate` method.                           |
| `string[] $stops`                       | An array of stop words provided to the `generate` method, or an empty array.    |
| `Vexo\LanguageModel\Response $response` | The response object containing the generated completions and response metadata. |
