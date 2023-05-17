<?php

declare(strict_types=1);

namespace Vexo\Chain\BranchingChain;

use Vexo\Chain\Context;
use Vexo\Contract\Event\Event;

final class ChainBranchConditionEvaluated implements Event
{
    public function __construct(
        private readonly string $chainIdentifier,
        private readonly string $chainClass,
        private readonly Context $context,
        private readonly string $condition,
        private readonly bool $evaluationResult
    ) {
    }

    public function chainIdentifier(): string
    {
        return $this->chainIdentifier;
    }

    public function chainClass(): string
    {
        return $this->chainClass;
    }

    public function context(): Context
    {
        return $this->context;
    }

    public function condition(): string
    {
        return $this->condition;
    }

    public function evaluationResult(): bool
    {
        return $this->evaluationResult;
    }
}
