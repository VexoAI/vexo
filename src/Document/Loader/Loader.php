<?php

declare(strict_types=1);

namespace Vexo\Document\Loader;

use Vexo\Document\Documents;

interface Loader
{
    public function load(): Documents;
}
