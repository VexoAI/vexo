<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;

final class SimpleInputFactoryTest extends TestCase
{
    public function testFromInput(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $input = new SimpleInput($data);
        $simpleInputFactory = new SimpleInputFactory();

        $createdInput = $simpleInputFactory->fromInput($input);

        $this->assertSame($data, $createdInput->data());
    }

    public function testFromOutput(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $output = new SimpleOutput($data);
        $simpleInputFactory = new SimpleInputFactory();

        $createdInput = $simpleInputFactory->fromOutput($output);

        $this->assertSame($data, $createdInput->data());
    }
}
