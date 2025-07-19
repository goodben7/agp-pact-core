<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use App\Dto\RequireAccessDto;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\MemberRepository;
use ApiPlatform\Metadata\ApiResource;
use App\State\RequireAccessProcessor;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: MemberRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_PHONE', fields: ['phone'])]
#[ORM\Table(name: '`member`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_MEMBER_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_MEMBER_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'member:post',],
            security: 'is_granted("ROLE_MEMBER_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Post(
            uriTemplate: "members/{id}/access",
            status: 200,
            security: 'is_granted("ROLE_USER_SET_ACCESS")',
            input: RequireAccessDto::class,
            processor: RequireAccessProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'member:patch',],
            security: 'is_granted("ROLE_MEMBER_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'member:get']
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'displayName' => 'ipartial',
    'company' => 'exact',
    'active' => 'exact',
    'jobTitle' => 'ipartial',
    'rank' => 'ipartial',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'contractStartDate', 'contractEndDate'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'contractStartDate', 'contractEndDate'])]
class Member
{
    public const ID_PREFIX = "ME";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['member:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $displayName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?Company $company = null;

    #[ORM\Column]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?bool $active = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['member:get', 'member:post'])]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(['member:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(['member:get'])]
    private ?string $userId = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $profilePicture = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?\DateTimeImmutable $contractStartDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?\DateTimeImmutable $contractEndDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $jobTitle = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['member:get', 'member:post', 'member:patch'])]
    private ?string $rank = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): \DateTimeImmutable|null
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function buildCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Get the value of active
     */
    public function isActive(): bool|null
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of userId
     */
    public function getUserId(): string|null
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */
    public function setUserId(?string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string|null
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */
    public function getPhone(): string|null
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getContractStartDate(): ?\DateTimeImmutable
    {
        return $this->contractStartDate;
    }

    public function setContractStartDate(?\DateTimeImmutable $contractStartDate): static
    {
        $this->contractStartDate = $contractStartDate;

        return $this;
    }

    public function getContractEndDate(): ?\DateTimeImmutable
    {
        return $this->contractEndDate;
    }

    public function setContractEndDate(?\DateTimeImmutable $contractEndDate): static
    {
        $this->contractEndDate = $contractEndDate;

        return $this;
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): static
    {
        $this->jobTitle = $jobTitle;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): static
    {
        $this->rank = $rank;

        return $this;
    }
}
