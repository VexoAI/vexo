<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use Vexo\Agent\Tool\Tool;

interface Resolver
{
    public function resolve(string $query, string $input): Tool;
}
