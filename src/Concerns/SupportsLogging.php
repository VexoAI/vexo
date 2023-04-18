<?php

declare(strict_types=1);

namespace Vexo\Weave\Concerns;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait SupportsLogging
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        if (!isset($this->logger)) {
            $this->setLogger(new NullLogger());
        }

        return $this->logger;
    }
}
