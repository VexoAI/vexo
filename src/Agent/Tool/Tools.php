<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool;

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
