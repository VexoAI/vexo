# OpenAI Chat

Vexo provides a Language Model implementation around the [OpenAI Chat API](https://platform.openai.com/docs/api-reference/chat).

## Prerequisites

You require an OpenAI account and API key to be able to use this model. You can create a key on your [API keys](https://platform.openai.com/account/api-keys) page once you are logged in at OpenAI.

## Instantiating

You can instantiate the model as follows, using the default `gpt-3.5-turbo` OpenAI model.

```php
// Replace this with your API key
$apiKey = getenv('OPENAI_API_KEY');

// Get a representation of the OpenAI Chat API
$chatApi = OpenAI::client($apiKey)->chat();

// Create the model
$model = new Vexo\LanguageModel\OpenAIChatLanguageModel($chatApi);
```

## Additional Parameters

If you want to override the model being used or provide other parameters, you can pass them along when instantiating your model.

```php
$model = new Vexo\LanguageModel\OpenAIChatLanguageModel(
  $chatApi,
  new Vexo\LanguageModel\Parameters([
    'model' => 'gpt-4',
    'temperature' => 0.8,
    'presence_penalty' => 0.5
  ])
);
```

For a full list of available parameters, please refer to the [OpenAI API reference](https://platform.openai.com/docs/api-reference/chat/create). Please note that the `stream` parameter is not supported at this time.

## Prompt prepending

You can add default prompts as model parameters which will be prepended to all requests. This is useful when you want to add system prompts needed to guide the model, but you don't want to include them with every call to the model.&#x20;

```php
$model = new Vexo\LanguageModel\OpenAIChatLanguageModel(
  $chatApi,
  new Vexo\LanguageModel\Parameters([
    'messages' => [
      ['role' => 'system', 'content' => 'You are concise in all your responses']
    ]
  ])
);
```
