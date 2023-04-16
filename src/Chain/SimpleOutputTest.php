<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Chain;

use PHPUnit\Framework\TestCase;

final class SimpleOutputTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $simpleOutput = new SimpleOutput($data);

        $this->assertSame($data, $simpleOutput->data());
    }
}
