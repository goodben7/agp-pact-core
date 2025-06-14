<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ActivityLogRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;

#[ORM\Entity(repositoryClass: ActivityLogRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'activity_log:get'],
            security: 'is_granted("ROLE_ACTIVITY_LOG_LIST")',
            provider: CollectionProvider::class
        ),
    ]
)]
class ActivityLog 
{
    public const ID_PREFIX = "AL";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['activity_log:get'])]
    private ?string $id = null;

    #[ORM\Column]
    #[Groups(['activity_log:get'])]
    private ?\DateTimeImmutable $timestamp = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['activity_log:get'])]
    private ?string $userId = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['activity_log:get'])]
    private ?string $entityType = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['activity_log:get'])]
    private ?string $entityId = null;

    #[ORM\Column(length: 255)]
    #[Groups(['activity_log:get'])]
    private ?string $activityType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['activity_log:get'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['activity_log:get'])]
    private ?array $details = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTimestamp(): ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getEntityType(): ?string
    {
        return $this->entityType;
    }

    public function setEntityType(?string $entityType): static
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(?string $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getActivityType(): ?string
    {
        return $this->activityType;
    }

    public function setActivityType(string $activityType): static
    {
        $this->activityType = $activityType;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): static
    {
        $this->details = $details;

        return $this;
    }
}
