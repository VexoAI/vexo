# Testing Models

Vexo provides a `Vexo\Model\FakeLanguageModel` which is a stub implementation of `Vexo\Model\LanguageModel`. You can use this for testing classes that depend on it.

```php
use Vexo\Model\FakeLanguageModel;
use Vexo\Model\Response;

$model = new FakeLanguageModel([
  new Response::fromString('The first response'),
  new Response::fromString('The second response')
]);

$model->generate('A prompt'); // Returns the first response
$model->generate('Another prompt'); // Returns the second response
$model->generate('Prompt unaccounted for'); // Throws a LogicException
```
