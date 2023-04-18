<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain;

use PHPUnit\Framework\TestCase;

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
    }
}
