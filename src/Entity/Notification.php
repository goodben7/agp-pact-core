<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use App\Enum\NotificationType;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Provider\UserNotificationProvider;
use App\Repository\NotificationRepository;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: 'is_granted("ROLE_NOTIFICATION_LIST")',
            provider: CollectionProvider::class
        ),
        new GetCollection(
            uriTemplate: '/notifications/me',
            normalizationContext: ['groups' => 'notification:get'],
            security: 'is_granted("ROLE_USER")',
            provider: UserNotificationProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_NOTIFICATION_DETAILS")',
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'notification:post'],
            security: 'is_granted("ROLE_NOTIFICATION_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'notification:patch'],
            security: 'is_granted("ROLE_NOTIFICATION_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'notification:get']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'type' => 'exact',
    'recipient' => 'exact',
    'recipientType' => 'exact',
    'isRead' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'readAt'])]
class Notification 
{
    public const ID_PREFIX = "NF";

    public const SENT_VIA_SYSTEM = 'system';
    public const SENT_VIA_SMS = 'sms';
    public const SENT_VIA_GMAIL = 'gmail';
    public const SENT_VIA_WHATSAPP = 'whatsapp';

    public const RECIPIENT_TYPE_USER = 'user';
    public const RECIPIENT_TYPE_EMAIL = 'email';
    public const RECIPIENT_TYPE_PHONE = 'phone';
    public const RECIPIENT_TYPE_WHATSAPP = 'whatsapp';
    public const RECIPIENT_TYPE_EXTERNAL_CONTACT = 'external_contact';
    
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['notification:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 10)]
    #[Groups(['notification:get', 'notification:post', 'notification:patch'])]
    #[Assert\Choice(callback: [NotificationType::class, 'getAll'], message: 'Invalid notification type.')]
    private ?string $type = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification:get', 'notification:post', 'notification:patch'])]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['notification:get', 'notification:post', 'notification:patch'])]
    private ?string $body = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['notification:get', 'notification:post', 'notification:patch'])]
    private ?array $data = null;

    #[ORM\Column]
    #[Groups(['notification:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['notification:get'])]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\Column]
    #[Groups(['notification:get', 'notification:patch'])]
    private bool $isRead = false;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['notification:get', 'notification:post', 'notification:patch'])]
    private ?string $sentVia = null;

    #[ORM\Column(length: 255)]
    #[Groups(['notification:get', 'notification:post'])]
    private ?string $recipient = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: [self::class, 'getRecipientTypeChoices'])]
    #[Groups(['notification:get', 'notification:post'])]
    private ?string $recipientType = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, NotificationType::getAll())) {
            throw new \InvalidArgumentException('Invalid notification type');
        }
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

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getReadAt(): ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): static
    {
        $this->readAt = $readAt;
        return $this;
    }

    public function isIsRead(): bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): static
    {
        $this->isRead = $isRead;
        if ($isRead && $this->readAt === null) {
            $this->readAt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function getSentVia(): ?string
    {
        return $this->sentVia;
    }

    public function setSentVia(?string $sentVia): static
    {
        $this->sentVia = $sentVia;
        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): static
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getRecipientType(): ?string
    {
        return $this->recipientType;
    }

    public function setRecipientType(string $recipientType): static
    {
        $this->recipientType = $recipientType;
        return $this;
    }

    public static function getRestrictionProperty(?string $type = null): string
    {
        return 'instance';
    }
    
    public static function getRecipientTypeChoices(): array
    {
        return [
            self::RECIPIENT_TYPE_USER,
            self::RECIPIENT_TYPE_EMAIL,
            self::RECIPIENT_TYPE_PHONE,
            self::RECIPIENT_TYPE_WHATSAPP,
            self::RECIPIENT_TYPE_EXTERNAL_CONTACT,
        ];
    }
}
