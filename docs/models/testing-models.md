# Testing Models

Vexo provides a stub implementation of `Vexo\Model\LanguageModel` which you can use for testing classes that depend on it.

```php
$model = new Vexo\Model\FakeLanguageModel([
  new Vexo\Model\Response::fromString('The first response'),
  new Vexo\Model\Response::fromString('The second response')
]);

$model->generate('A prompt'); // Returns the first response
$model->generate('Another prompt'); // Returns the second response
$model->generate('Prompt unaccounted for'); // Throws an exception
```
