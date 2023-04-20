<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns\SupportsCaching;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(NoCache::class)]
final class NoCacheTest extends TestCase
{
    private NoCache $noCache;

    protected function setUp(): void
    {
        $this->noCache = new NoCache();
    }

    public function testGet(): void
    {
        $this->assertSame('default_value', $this->noCache->get('test_key', 'default_value'));
    }

    public function testSet(): void
    {
        $this->assertTrue($this->noCache->set('test_key', 'test_value'));
    }

    public function testDelete(): void
    {
        $this->assertTrue($this->noCache->delete('test_key'));
    }

    public function testClear(): void
    {
        $this->assertTrue($this->noCache->clear());
    }

    public function testGetMultiple(): void
    {
        $this->assertSame(
            ['key1' => 'default_value', 'key2' => 'default_value'],
            $this->noCache->getMultiple(['key1', 'key2'], 'default_value')
        );
    }

    public function testSetMultiple(): void
    {
        $this->assertTrue($this->noCache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
    }

    public function testDeleteMultiple(): void
    {
        $this->assertTrue($this->noCache->deleteMultiple(['key1', 'key2']));
    }

    public function testHas(): void
    {
        $this->assertFalse($this->noCache->has('test_key'));
    }
}
