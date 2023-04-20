<?php

declare(strict_types=1);

namespace Vexo\Weave\Chain\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Vexo\Weave\Chain\Input;

#[CoversClass(SupportsInputValidation::class)]
final class SupportsInputValidationTest extends TestCase
{
    private object $supportsInputValidation;

    public function setUp(): void
    {
        $this->supportsInputValidation = new class() {
            use SupportsInputValidation;

            public function inputKeys(): array
            {
                return ['foo'];
            }

            public function process(Input $input): void
            {
                $this->validateInput($input);
            }
        };
    }

    public function testValidateInputThrowsRightException(): void
    {
        $this->expectException(SorryValidationFailed::class);
        $this->expectExceptionMessage('Input data is missing required key "foo". Recieved: bar');

        $this->supportsInputValidation->process(new Input(['bar' => 'baz']));
    }
}
