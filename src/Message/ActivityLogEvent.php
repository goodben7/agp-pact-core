<?php

namespace App\Message;

use App\Event\EventMessageInterface;

readonly class ActivityLogEvent implements EventMessageInterface
{
    public function __construct(
        private string  $activityType,
        private ?string $entityType = null,
        private ?string $entityId = null,
        private ?string $description = null,
        private ?array  $details = null,
        private ?string $userId = null,
    )
    {
    }

    public function getActivityType(): string
    {
        return $this->activityType;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}