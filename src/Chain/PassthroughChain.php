<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

final class PassthroughChain implements Chain
{
    public function __construct(private SimpleInputFactory $inputFactory)
    {
    }

    public function inputFactory(): InputFactory
    {
        return $this->inputFactory;
    }

    public function process(Input $input): Output
    {
        return new SimpleOutput($input->data());
    }
}