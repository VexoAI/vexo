<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use Nette\Utils\Json;

final class CommandResult
{
    public function __construct(
        public readonly array $data
    ) {
    }

    public function toJson(): string
    {
        return Json::encode($this->data);
    }
}