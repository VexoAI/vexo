<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

final class PassthroughChain implements Chain
{
    public function process(Input $input): Output
    {
        return new Output($input->data());
    }
}