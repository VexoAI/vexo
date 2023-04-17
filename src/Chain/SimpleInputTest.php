<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;

final class SimpleInputTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $simpleInput = new SimpleInput($data);

        $this->assertSame($data, $simpleInput->data());
    }
}
