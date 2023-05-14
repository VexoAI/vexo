<?php

declare(strict_types=1);

namespace Vexo\Chain;

use League\Event\EventDispatcher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\Attribute\RequiresContextValue;
use Vexo\Chain\Attribute\RequiresContextValuesMethod;
use Vexo\Contract\Event\Event;

#[CoversClass(SequentialRunner::class)]
#[IgnoreClassForCodeCoverage(RequiresContextValue::class)]
#[IgnoreClassForCodeCoverage(RequiresContextValuesMethod::class)]
final class SequentialRunnerTest extends TestCase
{
    public function testRun(): void
    {
        $runner = new SequentialRunner(chains: [
            new FakeChainWhichRequiresContextValue(),
            new FakeChain(['foo' => 'bar'])
        ]);

        $context = new Context(['some-variable' => 'fudge']);
        $runner->run($context);

        $this->assertSame(
            [
                'some-variable' => 'fudge',
                'foo' => 'bar'
            ],
            $context->toArray()
        );
    }

    public function testRunEmitsEvents(): void
    {
        $emittedEvents = [];

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->subscribeTo(
            Event::class,
            function (Event $event) use (&$emittedEvents): void {
                $emittedEvents[] = $event;
            }
        );

        $runner = new SequentialRunner(
            chains: [new FakeChain(['foo' => 'bar'])],
            eventDispatcher: $eventDispatcher
        );

        $context = new Context(['some-variable' => 'fudge']);
        $runner->run($context);

        $this->assertCount(2, $emittedEvents);

        $this->assertInstanceOf(ChainStarted::class, $emittedEvents[0]);
        $this->assertInstanceOf(ChainFinished::class, $emittedEvents[1]); // @phpstan-ignore-line
    }

    public function testRunThrowsExceptionIfRequiredContextValueIsMissing(): void
    {
        $runner = (new SequentialRunner())
            ->add(new FakeChainWhichRequiresContextValue());

        $context = new Context(); // Missing required 'some-variable' value

        $this->expectException(FailedToFindRequiredContextValueForChain::class);
        $runner->run($context);
    }

    public function testRunThrowsExceptionIfRequiredContextHasIncorrectType(): void
    {
        $runner = (new SequentialRunner())
            ->add(new FakeChainWhichRequiresContextValue());

        $context = new Context(['some-variable' => 23]); // Incorrect type

        $this->expectException(RequiredContextValueForChainHasIncorrectType::class);
        $runner->run($context);
    }

    public function testRunThrowsExceptionIfRequiredContextValueHasNonExistantClass(): void
    {
        $runner = (new SequentialRunner())
            ->add(new FakeChainWhichRequiresContextValueWithMissingClass());

        $context = new Context(['some-variable' => 23]);

        $this->expectException(RequiredContextValueForChainHasIncorrectType::class);
        $runner->run($context);
    }

    public function testRunThrowsExceptionIfRequiredContextValueSpecifiedThroughMethodIsMissing(): void
    {
        $runner = (new SequentialRunner())
            ->add(new FakeChainWhichSpecifiesRequiredContextValuesThroughMethod());

        $context = new Context(); // Missing required 'some-variable' value

        $this->expectException(FailedToFindRequiredContextValueForChain::class);
        $runner->run($context);
    }

    public function testRunThrowsExceptionIfRequiredContextValueSpecifiedThroughMethodHasIncorrectType(): void
    {
        $runner = (new SequentialRunner())
            ->add(new FakeChainWhichSpecifiesRequiredContextValuesThroughMethod());

        $context = new Context(['some-variable' => 23]); // Incorrect type

        $this->expectException(RequiredContextValueForChainHasIncorrectType::class);
        $runner->run($context);
    }
}

final class FakeChain implements Chain
{
    public function __construct(
        private readonly array $valuesToAddToContext = [],
    ) {
    }

    public function run(Context $context): void
    {
        foreach ($this->valuesToAddToContext as $name => $value) {
            $context->put($name, $value);
        }
    }
}

final class FakeChainWhichRequiresContextValue implements Chain
{
    #[Attribute\RequiresContextValue('some-variable', 'string')]
    public function run(Context $context): void
    {
    }
}

final class FakeChainWhichRequiresContextValueWithMissingClass implements Chain
{
    #[Attribute\RequiresContextValue('some-variable', 'Vexo\NonExistantClass')]
    public function run(Context $context): void
    {
    }
}

#[Attribute\RequiresContextValuesMethod('requiredContextValues')]
final class FakeChainWhichSpecifiesRequiredContextValuesThroughMethod implements Chain
{
    public function requiredContextValues(): array
    {
        return [
            'some-variable' => 'string'
        ];
    }

    public function run(Context $context): void
    {
    }
}
