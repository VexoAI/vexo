# Changelog

## 0.3.1

* Cleaned up library dependencies

## 0.3.0

* Lots of coding style fixes
* Renamed Vexo\Model to Vexo\LanguageModel
* Made FinishedGeneratingCompletion event have the whole response instead of just completions
* Added Vexo\LanguageModel\BaseLanguageModel to implement custom models
* Removed unneeded Vexo\LanguageModel\Parameters class
* Added first version of embeddings
* Added Tokenizer abstraction
* Simplified TextSplitters
* Added TokenTextSplitter that can split text by tokens

## 0.2.0

* FakeLanguageModel now also implements EventDispatcherAware for interoperability
* RegexOutputParser now has default prompt instructions
* Chain\Input and Chain\Output are now complete collection classes
* Added ability for WebTextChain to auto discover HTTP client
* Allow for injection of a custom TextExtractor in WebTextChain
* Added ability to override the default generated cache key prefix to CachingChain

## 0.1.0

* Initial release.
