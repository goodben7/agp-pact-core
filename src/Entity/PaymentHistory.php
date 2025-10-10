<?php

namespace App\Entity;

use App\Entity\Par;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Dto\CreatePaymentHistoryDto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PaymentHistoryRepository;
use App\State\CreatePaymentHistoryProcessor;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: PaymentHistoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    normalizationContext: ['groups' => 'payment_history:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_PAYMENT_HISTORY_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PAYMENT_HISTORY_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_PAYMENT_HISTORY_CREATE")',
            input: CreatePaymentHistoryDto::class,
            processor: CreatePaymentHistoryProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'payment_history:patch',],
            security: 'is_granted("ROLE_PAYMENT_HISTORY_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'par' => 'exact',
    'amount' => 'exact',
    'paymentMethod' => 'exact',
    'transactionReference' => 'partial'
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'paymentDate'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'paymentDate'])]
class PaymentHistory
{
    public const ID_PREFIX = "PH";
    
    public const PAYMENT_METHOD_CASH = "cash";
    public const PAYMENT_METHOD_BANK_TRANSFER = "bank_transfer";
    public const PAYMENT_METHOD_MOBILE_MONEY = "mobile_money";
    public const PAYMENT_METHOD_CHECK = "check";
    public const PAYMENT_METHOD_CREDIT_CARD = "credit_card";
    public const PAYMENT_METHOD_OTHER = "other";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['payment_history:get', 'par:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Par::class, inversedBy: 'paymentHistories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['payment_history:get'])]
    private ?Par $par = null;

    #[ORM\Column]
    #[Groups(['payment_history:get', 'payment_history:patch', 'par:get'])]
    private ?\DateTimeImmutable $paymentDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['payment_history:get', 'par:get'])]
    private ?string $amount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['payment_history:get', 'payment_history:patch'])]
    private ?string $transactionReference = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['payment_history:get'])]
    private ?string $paymentMethod = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['payment_history:get', 'payment_history:patch'])]
    private ?string $notes = null;

    #[ORM\Column]
    #[Groups(['payment_history:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPar(): ?Par
    {
        return $this->par;
    }

    public function setPar(?Par $par): static
    {
        $this->par = $par;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeImmutable
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTimeImmutable $paymentDate): static
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function setTransactionReference(?string $transactionReference): static
    {
        $this->transactionReference = $transactionReference;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}