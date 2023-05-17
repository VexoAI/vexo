<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain\Prompt;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToRenderPrompt::class)]
final class FailedToRenderPromptTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $failedToRenderPrompt = FailedToRenderPrompt::because($exception);

        $this->assertSame(
            'Failed to render prompt: Some exception',
            $failedToRenderPrompt->getMessage()
        );
        $this->assertSame($exception, $failedToRenderPrompt->getPrevious());
    }
}
