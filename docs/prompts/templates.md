# Templates

Prompt templates are used to construct prompts from a predefined format combined with relevant data.

## Basic Prompt Template

Vexo provides `Vexo\Prompt\BasicPromptTemplate` which can be used to construct prompts using basic templates. You provide the template, and any placeholder names which are to be replaced when rendering the template.

```php
$promptTemplate = new Vexo\Prompt\BasicPromptTemplate(
    'Act as a travel agent. Come up with a {{days}}-day itinerary for a trip to {{destination}}.',
    ['days', 'destination']
);
```

You can then render your prompt as follows:

```php
$prompt = $promptTemplate->render(['days' => 3, 'destination' => 'Barcelona, Spain']);
```

This will render a prompt containing the following:

```
Act as a travel agent. Come up with a 3-day itinerary for a trip to Barcelona, Spain.
```

A `Vexo\Prompt\SorryNotAllRequiredValuesWereGiven` exception will be thrown if not all the specified placeholders are passed to the call to `render()`.
