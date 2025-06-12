<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class InternalDecisionMadeMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $decisionId,
    )
    {
    }
}
