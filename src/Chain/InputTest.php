<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Input::class)]
final class InputTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $input = new Input($data);

        $this->assertSame($data, $input->data());
        $this->assertSame($data['key1'], $input->get('key1'));
        $this->assertSame('foo', $input->get('key3', 'foo'));
    }
}
