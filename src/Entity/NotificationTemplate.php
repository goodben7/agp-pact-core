<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiResource;
use App\Doctrine\IdGenerator;
use App\Enum\NotificationType;
use App\Repository\NotificationTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotificationTemplateRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_LIST")'
        ),
        new Get(
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_DETAILS")'
        ),
        new Post(
            denormalizationContext: ['groups' => 'notification_template:post'],
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_CREATE")',
        ),
        new Patch(
            denormalizationContext: ['groups' => 'notification_template:patch'],
            security: 'is_granted("ROLE_NOTIFICATION_TEMPLATE_UPDATE")',
        ),
    ],
    normalizationContext: ['groups' => 'notification_template:get']
)]
class NotificationTemplate
{
    public const ID_PREFIX = "NT";

    public const RECIPIENT_PROFILE_USERS = 'profile_users';
    public const RECIPIENT_COMPLAINANT = 'complainant';
    public const RECIPIENT_INVOLVED_COMPANY = 'involved_company';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['notification_template:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [NotificationType::class, 'getAll'], message: 'Invalid notification type.')]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $triggerEvent = null;

    #[ORM\Column]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?bool $active = true;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $senderEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $senderName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?string $sentVia = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?bool $isSensitive = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    private ?Profile $profile = null;

    #[ORM\Column(type: Types::JSON, options: ['default' => '[]'])]
    #[Groups(['notification_template:get', 'notification_template:post', 'notification_template:patch'])]
    #[Assert\Choice(
        choices: [
            self::RECIPIENT_PROFILE_USERS,
            self::RECIPIENT_COMPLAINANT,
            self::RECIPIENT_INVOLVED_COMPANY,
        ],
        multiple: true,
        message: 'Le sélecteur de destinataire "{{ value }}" n\'est pas valide.'
    )]
    private array $recipientSelectors = [];

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

    public function getIsSensitive(): ?bool
    {
        return $this->isSensitive;
    }

    public function setIsSensitive(?bool $isSensitive): static
    {
        $this->isSensitive = $isSensitive;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    public function getRecipientSelectors(): array
    {
        return $this->recipientSelectors;
    }

    public function setRecipientSelectors(array $recipientSelectors): static
    {
        $this->recipientSelectors = $recipientSelectors;

        return $this;
    }
}
