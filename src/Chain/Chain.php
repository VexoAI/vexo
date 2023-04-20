<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

interface Chain
{
    /**
     * @return string[]
     */
    public function inputKeys(): array;

    /**
     * @return string[]
     */
    public function outputKeys(): array;

    public function process(Input $input): Output;
}
