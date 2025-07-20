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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

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
            denormalizationContext: ['groups' => 'member:post'],
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
            denormalizationContext: ['groups' => 'member:patch'],
            security: 'is_granted("ROLE_MEMBER_UPDATE")',
            processor: PersistProcessor::class,
        ),
        new Post(
            uriTemplate: '/members/{id}/profile_picture',
            inputFormats: ['multipart' => ['multipart/form-data']],
            normalizationContext: ['groups' => ['member:get']],
            denormalizationContext: ['groups' => ['member:picture:post']],
            security: "is_granted('ROLE_MEMBER_UPDATE', object)",
            processor: PersistProcessor::class
        )
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
#[Vich\Uploadable]
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

    #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePicture')]
    #[Groups(['member:picture:post'])]
    private ?File $profilePictureFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['member:get'])]
    private ?string $profilePicture = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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
    private ?string $position = null;

    public function __construct()
    {
        if (is_null($this->createdAt))
            $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function setProfilePictureFile(?File $profilePictureFile = null): void
    {
        $this->profilePictureFile = $profilePictureFile;
        if (null !== $profilePictureFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function setProfilePicture(?string $profilePicture): static
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): static
    {
        $this->position = $position;

        return $this;
    }
}

