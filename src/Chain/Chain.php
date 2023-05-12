<?php

declare(strict_types=1);

namespace Vexo\Chain;

interface Chain
{
    public function run(Context $context): void;
}
