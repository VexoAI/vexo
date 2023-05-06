<?php

declare(strict_types=1);

namespace Vexo\DocumentLoader;

use Vexo\Contract\Document\Documents;

interface Loader
{
    public function load(): Documents;
}
