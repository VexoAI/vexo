# Simple Prompts

Prompts are input provided to models, which serve as a starting point or a cue for the model to generate a response in a particular manner.

Prompts are usually constructed from different components to provide the model with instructions which are context-aware, and help the model respond in a format which can be easily parsed.

Vexo provides a basic `Prompt` value object which you can use to wrap your prompt.

```php
$prompt = new Vexo\Prompt\Prompt('What is the capital of France?');
```

Right now Vexo only supports text-based prompts. In the future this may be expanded to include other types of input like images.
