<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Command;

use PHPUnit\Framework\TestCase;

class CommandBuilderTest extends TestCase
{
    private CommandBuilder $commandBuilder;

    protected function setUp(): void
    {
        $this->commandBuilder = new CommandBuilder([
            'Pragmatist\\Assistant\\Command\\'
        ]);
    }

    public function testFromArray(): void
    {
        $command = $this->commandBuilder->fromArray([
            'name' => 'do_nothing',
            'args' => ['reason' => 'No further action is required.']
        ]);

        $this->assertInstanceOf(Command::class, $command);
        $this->assertEquals(['reason' => 'No further action is required.'], $command->arguments());
    }

    public function testFromArrayWithMissingKeys(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->commandBuilder->fromArray([
            'name' => 'do_nothing',
            // 'args' key is missing
        ]);
    }

    public function testGetCommandClassWithMissingClass(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->commandBuilder->fromArray([
            'name' => 'non_existant_command',
            'args' => []
        ]);
    }
}
