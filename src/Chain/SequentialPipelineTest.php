<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

use PHPUnit\Framework\TestCase;

final class SequentialPipelineTest extends TestCase
{
    public function testProcess(): void
    {
        $inputData = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $input = new SimpleInput($inputData);

        $inputFactory = new SimpleInputFactory();
        $passthroughChainOne = new PassthroughChain($inputFactory);
        $passthroughChainTwo = new PassthroughChain($inputFactory);
        $sequentialPipeline = new SequentialPipeline($passthroughChainOne, $passthroughChainTwo);

        $output = $sequentialPipeline->process($input);

        $this->assertSame($inputData, $output->data());
    }
}
