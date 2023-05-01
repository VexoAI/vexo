<?php

declare(strict_types=1);

namespace Vexo\Agent;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Step>
 */
final class Steps extends AbstractCollection
{
    public function getType(): string
    {
        return Step::class;
    }
}
