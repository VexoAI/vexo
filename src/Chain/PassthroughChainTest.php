<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;

final class PassthroughChainTest extends TestCase
{
    public function testProcess(): void
    {
        $inputData = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $input = new Input($inputData);
        $passthroughChain = new PassthroughChain();

        $output = $passthroughChain->process($input);

        $this->assertSame($inputData, $output->data());
    }
}
