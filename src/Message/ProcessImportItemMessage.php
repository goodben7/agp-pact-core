<?php

namespace App\Message;

use App\Event\EventMessageInterface;

readonly class ProcessImportItemMessage implements EventMessageInterface
{
    public function __construct(
        private string $itemId
    )
    {
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }
}