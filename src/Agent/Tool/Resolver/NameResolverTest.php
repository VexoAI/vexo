<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\Tool;
use Vexo\Agent\Tool\Tools;

#[CoversClass(NameResolver::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupStarted::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupFinished::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupFailed::class)]
final class NameResolverTest extends TestCase
{
    private NameResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new NameResolver(
            new Tools([new Callback('my_tool', 'Useful for things', fn ($input): string => 'Received: ' . $input)])
        );
    }

    public function testLookup(): void
    {
        $this->assertInstanceOf(Tool::class, $this->resolver->resolve('my_tool', 'some input'));
        $this->assertInstanceOf(Tool::class, $this->resolver->resolve('MY_TOOL', 'some input'));
        $this->assertInstanceOf(Tool::class, $this->resolver->resolve(' My_Tool ', 'some input'));
    }

    public function testLookupFails(): void
    {
        $this->expectException(FailedToResolveTool::class);
        $this->expectExceptionMessage('Failed to resolve tool unknown_tool: Name not found');
        $this->resolver->resolve('unknown_tool', 'some input');
    }
}
