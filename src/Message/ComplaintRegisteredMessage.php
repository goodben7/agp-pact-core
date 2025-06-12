<?php

namespace App\Message;

use App\Event\EventMessageInterface;

class ComplaintRegisteredMessage implements EventMessageInterface
{
    public function __construct(
        public string $complaintId,
        public string $complaintEmail,
        public string $complaintPhone,
    )
    {
    }

    public function getComplaintId(): string
    {
        return $this->complaintId;
    }

    public function getComplaintEmail(): string
    {
        return $this->complaintEmail;
    }

    public function getComplaintPhone(): string
    {
        return $this->complaintPhone;
    }
}
