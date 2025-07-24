<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Enum\NotificationType;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use App\Repository\NotificationTemplateRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: NotificationTemplateRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_LIST")',
            provider: CollectionProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_DETAILS")',
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'notification_template:post'],
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'notification_template:patch'],
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'notification_template:get']
)]
class NotificationTemplate
{
    public const ID_PREFIX = "NT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['notification_template:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 120)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(callback: [NotificationType::class, 'getAll'], message: 'Invalid notification type.')]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $triggerEvent = null;

    #[ORM\Column]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $senderEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $senderName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $sentVia = null;

    #[ORM\Column]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?bool $isSensitive = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?Profile $profile = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getTriggerEvent(): ?string
    {
        return $this->triggerEvent;
    }

    public function setTriggerEvent(string $triggerEvent): static
    {
        $this->triggerEvent = $triggerEvent;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getSenderEmail(): ?string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(?string $senderEmail): static
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    public function getSenderName(): ?string
    {
        return $this->senderName;
    }

    public function setSenderName(?string $senderName): static
    {
        $this->senderName = $senderName;

        return $this;
    }

    public function getSentVia(): ?string
    {
        return $this->sentVia;
    }

    public function setSentVia(string $sentVia): static
    {
        $this->sentVia = $sentVia;

        return $this;
    }

    /**
     * Get the value of isSensitive
     */ 
    public function getIsSensitive(): bool|null
    {
        return $this->isSensitive;
    }

    /**
     * Set the value of isSensitive
     *
     * @return  self
     */ 
    public function setIsSensitive(?bool $isSensitive): static
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    /**
     * Get the value of profile
     */ 
    public function getProfile(): Profile|null
    {
        return $this->profile;
    }

    /**
     * Set the value of profile
     *
     * @return  self
     */ 
    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }
}
