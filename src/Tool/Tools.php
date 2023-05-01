<?php

declare(strict_types=1);

namespace Vexo\Tool;

use Ramsey\Collection\AbstractCollection;

/**
 * @extends AbstractCollection<Tool>
 */
final class Tools extends AbstractCollection
{
    public function getType(): string
    {
        return Tool::class;
    }
}
