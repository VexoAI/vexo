<?php

declare(strict_types=1);

namespace Pragmatist\Assistant\Prompt;

use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    public function testSimple(): void
    {
        $engine = new StrReplaceRenderEngine();
        $builder = new Builder($engine);

        $simplePrompt = $builder->simple('test_template');

        $this->assertInstanceOf(SimplePrompt::class, $simplePrompt);
    }
}