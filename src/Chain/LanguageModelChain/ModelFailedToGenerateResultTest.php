<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ModelFailedToGenerateResult::class)]
final class ModelFailedToGenerateResultTest extends TestCase
{
    public function testFactoryMethod(): void
    {
        $exception = new \Exception('Some exception');

        $modelFailedToGenerateResult = ModelFailedToGenerateResult::because($exception);

        $this->assertSame(
            'Model failed to generate result: Some exception',
            $modelFailedToGenerateResult->getMessage()
        );
        $this->assertSame($exception, $modelFailedToGenerateResult->getPrevious());
    }
}
