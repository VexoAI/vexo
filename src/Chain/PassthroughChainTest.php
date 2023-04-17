<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;

final class PassthroughChainTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $simpleInputFactory = new SimpleInputFactory();
        $passthroughChain = new PassthroughChain($simpleInputFactory);

        $this->assertSame($simpleInputFactory, $passthroughChain->inputFactory());
    }

    public function testProcess(): void
    {
        $inputData = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $input = new SimpleInput($inputData);
        $passthroughChain = new PassthroughChain(new SimpleInputFactory());

        $output = $passthroughChain->process($input);

        $this->assertSame($inputData, $output->data());
    }
}
