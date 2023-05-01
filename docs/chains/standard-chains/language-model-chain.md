# Language Model Chain

This chain enables running queries against a given language model using a prompt template.

You can use it as follows.

```php
use Vexo\Chain\Input;
use Vexo\Chain\LanguageModelChain;
use Vexo\LanguageModel\FakeLanguageModel;
use Vexo\LanguageModel\Response;
use Vexo\Prompt\BasicPromptTemplate;

// Replace with instance of a real language model
$languageModel = new FakeLanguageModel(
    Response::fromString('I would advise you to stay at the airport.'),
);

// Create our basic prompt template
$promptTemplate = new BasicPromptTemplate(
    'You are a travel advisor. Give me an itinerary for a day trip to {{text}}.',
    ['text']
);

$chain = new LanguageModelChain(
    languageModel: $languageModel,
    promptTemplate: $promptTemplate
);

// Call the chain
$output = $chain->process(
    new Input(['text' => 'Amsterdam'])
);

// Outputs: I would advise you to stay at the airport.
echo $output['text'];
```

## Customizing Input and Output

Sometimes you want to provide more parameters than a single text input, or you would like the output variable to be more appropriately named.

You can change the inputs by providing an array of names in the `inputKeys` constructor argument. You can change the name of the output variable by providing the `outputKey` argument.

This will allow us to use more relevant names in the prompt template as well, input, and output processing as well:

```php
// Create our basic prompt template
$promptTemplate = new BasicPromptTemplate(
    'You are a travel advisor. Give me an itinerary for a {{days}}-day trip to {{destination}}.',
    ['destination', 'days']
);

$chain = new LanguageModelChain(
    languageModel: $languageModel,
    promptTemplate: $promptTemplate,
    inputKeys: ['destination', 'days'],
    outputKey: ['advice']
);

// Call the chain
$output = $chain->process(
    new Input(['destination' => 'Amsterdam', 'days' => 3])
);

// Outputs: I would advise you to stay at the airport.
echo $output['advice'];
```

## Stops

In some scenarios you want the language model to not generate beyond certain stop words. In that case you provide an array of stop token strings in the `stops` argument.


```php
$chain = new LanguageModelChain(
    languageModel: $languageModel,
    promptTemplate: $promptTemplate,
    stops: ['Observation:']
);
```
