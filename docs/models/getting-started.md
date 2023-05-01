# Getting Started

Models in Vexo all implement the `Vexo\LanguageModel\LanguageModel` interface which provides a generic way to interact with language models.

```php
LanguageModel::generate(Prompt $prompt, string ...$stops): Response
```

The `generate` method takes a prompt, and optionally one or more stop terms. It then produces a response containing the output of the language model and some additional metadata.

Have a look at [Providers](providers/) to understand how to instantiate a new model of a particular provider. Once you have instantiated your model, you can simply use it as follows:

```php
// Assumes $model is an instance of \Vexo\LanguageModel\LanguageModel
$response = $model->generate(
    new \Vexo\Prompt('What is the capital of France?')
);

// Would contain the text response. E.g. "The capital of France is Paris."
$text = (string) $response->completions();
```

## 1. Creating the Prompt

You can create a prompt by simply instantiating a new instance of `Prompt` as follows:

```php
$prompt = new \Vexo\Prompt('What is the capital of France?');
```

### Using Prompt templates

Optionally, if you want to have a reusable prompt, you can also make use of a `PromptTemplate` and then construct the prompt when you're ready to call the model. To create the above prompt using a template instead, you can do the following:

```php
$promptTemplate = new \Vexo\Prompt\BasicPromptTemplate(
    'What is the capital of {{country}}?',
    ['country']
);

$prompt = $promptTemplate->render(['country' => 'France']);
```

Currently Vexo only comes with the `BasicPromptTemplate`, but more may be added in the future.

## 2. Calling the Language Model

Once you have the prompt you can call the model to generate a response.

```php
// $model is an instance of \Vexo\LanguageModel\LanguageModel
$response = $model->generate($prompt);
```

### Using stops

Optionally, if you want to make sure that the model stops generating as soon as it encounters particular tokens in its completion, you can provide them with the call. This is useful if your model tends to produce more output than is generally desired.

For instance, if we only want a brief answer to our question above, but our model usually responds with something as follows:

> The capital of France is Paris. Paris is a beautiful city known for its stunning architecture, rich history, vibrant culture, world-class museums, and romantic atmosphere.

We can force it to stop generating after the first sentence by providing `'. '` as a stop token.

```php
$response = $model->generate($prompt, '. ');
```

The response will now simply contain the completion "The capital of France is Paris."

## 3. Interpreting the Response

If you just want the text you can call the `completions` method on the response to return an instance of the `Vexo\LanguageModel\Completions` collection containing the generated text, which you can cast to a string.

```php
$text = (string) $response->completions();
```

### Multiple completions

Some models are able to provide multiple alternate completions for the same prompt. The collection implements both PHP's [`ArrayAccess`](https://www.php.net/manual/en/class.arrayaccess.php) and [`IteratorAggregate`](https://www.php.net/manual/en/class.iteratoraggregate) for you to interact with the different completions should you need to.

### Response metadata

You can get additional metadata related to the response by calling the `metadata` method. The data returned is model-specific so you cannot assume its structure across different models.

```php
$metadata = $response->metadata();

print_r($metadata->toArray());
```

The above would output something like this, depending on the model being used:

```
Array
(
  [model] => gpt-3.5-turbo
  [usage] => Array
    (
      [prompt_tokens] => 9
      [completion_tokens] => 12
      [total_tokens] => 21
    )
)
```
