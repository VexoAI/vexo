<?php

declare(strict_types=1);

namespace Vexo\Contract\Document;

use Vexo\Contract\Metadata\Metadata;

interface Document
{
    public function contents(): string;

    public function metadata(): Metadata;
}
