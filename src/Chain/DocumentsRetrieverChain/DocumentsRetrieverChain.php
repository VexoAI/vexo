<?php

declare(strict_types=1);

namespace Vexo\Chain\DocumentsRetrieverChain;

use Vexo\Chain\Chain;
use Vexo\Chain\Context;
use Vexo\Chain\DocumentsRetrieverChain\Retriever\Retriever;

final class DocumentsRetrieverChain implements Chain
{
    public function __construct(
        private readonly Retriever $retriever
    ) {
    }

    public function run(Context $context): void
    {
        /** @var string $query */
        $query = $context->get('query');

        $context->put(
            'documents',
            $this->retriever->retrieve($query)
        );
    }
}
