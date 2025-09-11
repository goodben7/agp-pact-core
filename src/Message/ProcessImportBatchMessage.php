<?php

namespace App\Message;

use App\Event\EventMessageInterface;

readonly class ProcessImportBatchMessage implements EventMessageInterface
{
    public function __construct(
        private string $batchId
    )
    {
    }

    public function getBatchId(): string
    {
        return $this->batchId;
    }
}