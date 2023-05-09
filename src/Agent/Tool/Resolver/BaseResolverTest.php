<?php

declare(strict_types=1);

namespace Vexo\Agent\Tool\Resolver;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreClassForCodeCoverage;
use PHPUnit\Framework\TestCase;
use Vexo\Agent\Tool\Callback;
use Vexo\Agent\Tool\Tool;
use Vexo\Agent\Tool\Tools;

#[CoversClass(BaseResolver::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupStarted::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupFinished::class)]
#[IgnoreClassForCodeCoverage(ResolverLookupFailed::class)]
final class BaseResolverTest extends TestCase
{
    private BaseResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new BaseResolverStub(
            new Tools([new Callback('my_tool', 'Useful for things', fn ($input): string => 'Received: ' . $input)])
        );
    }

    public function testLookup(): void
    {
        $this->assertInstanceOf(Tool::class, $this->resolver->resolve('my_tool', 'some input'));
    }

    public function testLookupFails(): void
    {
        $this->expectException(FailedToResolveTool::class);
        $this->expectExceptionMessage('Failed to resolve tool unknown_tool: Invalid query');
        $this->resolver->resolve('unknown_tool', 'some input');
    }
}

final class BaseResolverStub extends BaseResolver
{
    protected function lookup(string $query, string $input): Tool
    {
        if ($query == 'my_tool') {
            return $this->tools[0];
        }

        throw new \RuntimeException('Invalid query');
    }
}
