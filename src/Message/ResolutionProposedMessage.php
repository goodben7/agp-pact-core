<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ResolutionProposedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
    )
    {
    }
}
