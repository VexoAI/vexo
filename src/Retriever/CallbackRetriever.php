<?php

declare(strict_types=1);

namespace Vexo\Retriever;

use Vexo\Contract\Document\Documents;

final class CallbackRetriever implements Retriever
{
    /**
     * @var callable(string): Documents
     */
    private $retrieverFunction;

    /**
     * @param callable(string): Documents $retrieverFunction
     */
    public function __construct(
        callable $retrieverFunction
    ) {
        $this->retrieverFunction = $retrieverFunction;
    }

    public function retrieve(string $query): Documents
    {
        return ($this->retrieverFunction)($query);
    }
}
