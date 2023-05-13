<?php

declare(strict_types=1);

namespace Vexo\Chain\LanguageModelChain;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Vexo\Chain\LanguageModelChain\Blueprint\QuestionAnswerBlueprint;

#[CoversClass(QuestionAnswerBlueprint::class)]
final class BlueprintTest extends TestCase
{
    private const BLUEPRINT_CLASSES = [
        QuestionAnswerBlueprint::class,
    ];

    public static function blueprintProvider(): array
    {
        return array_map(
            fn (string $blueprintClass): array => [new $blueprintClass()],
            self::BLUEPRINT_CLASSES
        );
    }

    #[DataProvider('blueprintProvider')]
    public function testBlueprint(Blueprint $blueprint): void
    {
        $this->assertNotEmpty($blueprint->promptTemplate());
        $this->assertContainsOnly('string', $blueprint->requiredContextValues());
        $this->assertContainsOnly('string', $blueprint->stops());
    }
}
