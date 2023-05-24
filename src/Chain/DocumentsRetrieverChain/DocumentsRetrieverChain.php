<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\ContextAssert;
use Vexo\Document\Retriever\Retriever;

final class DocumentsRetrieverChain implements Chain
{
    private const INPUT_QUERY = 'query';
    private const OUTPUT_DOCUMENTS = 'documents';

    /**
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function __construct(
        private readonly Retriever $retriever,
        private readonly int $maxResults = 4,
        private readonly array $inputMap = [],
        private readonly array $outputMap = []
    ) {
    }

    public function run(Context $context): void
    {
        /** @var string $query */
        $query = $context->get($this->inputMap[self::INPUT_QUERY] ?? self::INPUT_QUERY);
        ContextAssert::stringNotEmpty($query);

        $documents = $this->retriever->retrieve($query, $this->maxResults);

        $context->put($this->outputMap[self::OUTPUT_DOCUMENTS] ?? self::OUTPUT_DOCUMENTS, $documents);
    }
}
