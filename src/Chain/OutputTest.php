<?php

declare(strict_types=1);

namespace Vexo\Chain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Output::class)]
final class OutputTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $output = new Output($data);

        $this->assertSame($data, $output->data());
        $this->assertSame($data['key1'], $output->get('key1'));
        $this->assertSame('foo', $output->get('key3', 'foo'));
    }
}
