<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class MeritsExaminedMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $meritsAnalysis,
    )
    {
    }
}
