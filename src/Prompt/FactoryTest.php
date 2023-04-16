<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testSimple(): void
    {
        $engine = new StrReplaceRenderEngine();
        $builder = new Factory($engine);

        $simplePrompt = $builder->simple('test_template');

        $this->assertInstanceOf(SimplePrompt::class, $simplePrompt);
    }
}