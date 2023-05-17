<?php

declare(strict_types=1);

namespace Vexo\Chain\ConcatenateDocumentsChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Contract\Document\Documents;

final class ConcatenateDocumentsChain implements Chain
{
    private const INPUT_DOCUMENTS = 'documents';
    private const OUTPUT_COMBINED_CONTENTS = 'combined_contents';

    /**
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function __construct(
        private readonly array $inputMap = [],
        private readonly array $outputMap = []
    ) {
    }

    public function run(Context $context): void
    {
        /** @var Documents $documents */
        $documents = $context->get($this->inputMap[self::INPUT_DOCUMENTS] ?? self::INPUT_DOCUMENTS);

        $combinedContents = implode(
            "\n\n",
            array_map(fn ($document): string => $document->contents(), $documents->toArray())
        );

        $context->put($this->outputMap[self::OUTPUT_COMBINED_CONTENTS] ?? self::OUTPUT_COMBINED_CONTENTS, $combinedContents);
    }
}
