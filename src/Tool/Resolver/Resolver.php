<?php

declare(strict_types=1);

namespace Vexo\Tool\Resolver;

use Vexo\Tool\Tool;

interface Resolver
{
    public function resolve(string $query, string $input): Tool;
}
