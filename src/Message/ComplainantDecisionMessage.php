<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplainantDecisionMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $decisionId,
    )
    {
    }
}
