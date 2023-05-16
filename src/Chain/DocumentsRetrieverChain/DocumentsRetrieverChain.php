<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\ContextValueMapperBehavior;
use Vexo\Chain\DocumentsRetrieverChain\Retriever\Retriever;

final class DocumentsRetrieverChain implements Chain
{
    use ContextValueMapperBehavior;

    private const INPUT_QUERY = 'query';
    private const OUTPUT_DOCUMENTS = 'documents';

    /**
     * @param array<string, string> $inputMap
     * @param array<string, string> $outputMap
     */
    public function __construct(
        private readonly Retriever $retriever,
        private readonly array $inputMap = [],
        private readonly array $outputMap = []
    ) {
    }

    public function run(Context $context): void
    {
        /** @var string $query */
        $query = $this->get($context, self::INPUT_QUERY);

        $this->put($context, self::OUTPUT_DOCUMENTS, $this->retriever->retrieve($query));
    }
}
