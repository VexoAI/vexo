<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Commands;

use PHPUnit\Framework\TestCase;

final class CommandResultTest extends TestCase
{
    public function testConstructorSetsData(): void
    {
        $data = ['key' => 'value'];
        $commandResult = new CommandResult($data);

        $this->assertSame($data, $commandResult->data);
    }

    public function testToJsonReturnsJsonEncodedData(): void
    {
        $data = ['key' => 'value'];
        $commandResult = new CommandResult($data);

        $json = $commandResult->toJson();
        $this->assertSame(json_encode($data), $json);
    }
}