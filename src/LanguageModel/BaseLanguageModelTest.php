<?php

declare(strict_types=1);

namespace Vexo\LanguageModel;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Event\SomethingHappened;
use Vexo\Prompt\Prompt;

#[CoversClass(BaseLanguageModel::class)]
#[IgnoreClassForCodeCoverage(StartedGeneratingCompletion::class)]
#[IgnoreClassForCodeCoverage(FinishedGeneratingCompletion::class)]
final class BaseLanguageModelTest extends TestCase
{
    public function testGenerate(): void
    {
        $collectedEvents = [];
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            SomethingHappened::class,
            function (SomethingHappened $event) use (&$collectedEvents): void {
                $collectedEvents[] = $event;
            }
        );

        $model = new class() extends BaseLanguageModel {
            protected function call(Prompt $prompt, string ...$stops): Response
            {
                return new Response(
                    new Completions([new Completion('The capital of France is Paris.')]),
                    new ResponseMetadata(['prompt' => (string) $prompt])
                );
            }
        };
        $model->useEventDispatcher($eventDispatcher);

        $response = $model->generate(new Prompt('What is the capital of France?'));

        $this->assertEquals('The capital of France is Paris.', (string) $response->completions());
        $this->assertEquals('What is the capital of France?', $response->metadata()->get('prompt'));
        $this->assertCount(2, $collectedEvents);
        $this->assertInstanceOf(StartedGeneratingCompletion::class, array_shift($collectedEvents));
        $this->assertInstanceOf(FinishedGeneratingCompletion::class, array_shift($collectedEvents));
    }
}
