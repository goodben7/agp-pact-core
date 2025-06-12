<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class SatisfactionFollowedUpMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $satisfactionResultId,
    )
    {
    }
}
