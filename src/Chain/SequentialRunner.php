<?php

declare(strict_types=1);

namespace Vexo\Chain;

use Psr\EventDispatcher\EventDispatcherInterface;
use Vexo\Contract\Event\Event;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

final class SequentialRunner implements Runner
{
    /**
     * @var array<string, Chain>
     */
    private array $chains = [];

    /**
     * @var array<string, array<string, ?string>>
     */
    private array $requiredContextValues = [];

    public function __construct(
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        array $chains = []
    ) {
        foreach ($chains as $chain) {
            $this->add($chain);
        }
    }

    public function add(Chain $chain): self
    {
        $identifier = spl_object_hash($chain);

        $this->chains[$identifier] = $chain;
        $this->determineContextValuesForChain($identifier, $chain);

        return $this;
    }

    public function run(Context $context): void
    {
        foreach ($this->chains as $identifier => $chain) {
            $this->ensureCurrentContextIsValidForChain($context, $chain, $identifier);
            $this->emit(new ChainStarted($identifier, $chain::class, $context));
            $chain->run($context);
            $this->emit(new ChainFinished($identifier, $chain::class, $context));
        }
    }

    private function emit(Event $event): void
    {
        if ($this->eventDispatcher instanceof EventDispatcherInterface) {
            $this->eventDispatcher->dispatch($event);
        }
    }

    private function ensureCurrentContextIsValidForChain(Context $context, Chain $chain, string $identifier): void
    {
        foreach ($this->requiredContextValues[$identifier] as $requiredContextValue => $requiredContextValueType) {
            if ( ! $context->containsKey($requiredContextValue)) {
                throw FailedToFindRequiredContextValueForChain::with($requiredContextValue, $chain::class, $identifier);
            }

            if (null === $requiredContextValueType) {
                continue;
            }

            try {
                $this->validateContextValueType($requiredContextValueType, $context->get($requiredContextValue));
            } catch (InvalidArgumentException $exception) {
                throw RequiredContextValueForChainHasIncorrectType::with($requiredContextValue, $chain::class, $identifier, $exception);
            }
        }
    }

    private function validateContextValueType(string $requiredContextValueType, mixed $value): void
    {
        match ($requiredContextValueType) {
            'string' => Assert::string($value),
            'int', 'integer' => Assert::integer($value),
            'float' => Assert::float($value),
            'bool' => Assert::boolean($value),
            'array' => Assert::isArray($value),
            'object' => Assert::object($value),
            'mixed' => true, // no-op
            default => Assert::isInstanceOf($value, $requiredContextValueType)
        };
    }

    private function determineContextValuesForChain(string $identifier, Chain $chain): void
    {
        $this->requiredContextValues[$identifier] = [];

        foreach ((new \ReflectionMethod($chain, 'run'))->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute\RequiresContextValue::class) {
                $this->requiredContextValues[$identifier][$attribute->newInstance()->name] = $attribute->newInstance()->type;
            }
        }

        foreach ((new \ReflectionClass($chain))->getAttributes() as $attribute) {
            if ($attribute->getName() === Attribute\RequiresContextValuesMethod::class) {
                $requiredContextValues = $chain->{$attribute->newInstance()->methodName}();
                foreach ($requiredContextValues as $name => $type) {
                    $this->requiredContextValues[$identifier][$name] = $type;
                }
            }
        }
    }
}
