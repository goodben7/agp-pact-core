<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\OffenderRepository;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: OffenderRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_OFFENDER_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_OFFENDER_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'offender:post',],
            security: 'is_granted("ROLE_OFFENDER_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'offender:patch',],
            security: 'is_granted("ROLE_OFFENDER_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'offender:get']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'lastName' => 'ipartial',
        'firstName' => 'ipartial',
        'middleName' => 'ipartial',
        'fullName' => 'ipartial',
        'gender.code' => 'exact',
        'gender.category' => 'exact',
        'complaint' => 'exact',
    ]
)]
class Offender
{
    public const ID_PREFIX = "OF";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['offender:get', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['offender:get', 'complaint:get', 'complaint:list'])]
    private ?string $fullName = null;

    #[ORM\ManyToOne]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $gender = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'offenders')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['offender:get', 'offender:post'])]
    private ?Complaint $complaint = null;

    #[ORM\ManyToOne]
    #[ORM\Column(nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $relationshipProject = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['offender:get', 'offender:post', 'offender:patch', 'complaint:get', 'complaint:list'])]
    private ?int $age = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): static
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getGender(): ?GeneralParameter
    {
        return $this->gender;
    }

    public function setGender(?GeneralParameter $gender): static
    {
        $this->gender = $gender;

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

    public function getComplaint(): ?Complaint
    {
        return $this->complaint;
    }

    public function setComplaint(?Complaint $complaint): static
    {
        $this->complaint = $complaint;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function buildFullName()
    {
        return $this->fullName = $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    /**
     * Get the value of relationshipProject
     */ 
    public function getRelationshipProject(): GeneralParameter|null
    {
        return $this->relationshipProject;
    }

    /**
     * Set the value of relationshipProject
     *
     * @return  self
     */ 
    public function setRelationshipProject(?GeneralParameter $relationshipProject): static
    {
        $this->relationshipProject = $relationshipProject;

        return $this;
    }

    /**
     * Get the value of age
     */ 
    public function getAge(): int|null
    {
        return $this->age;
    }

    /**
     * Set the value of age
     *
     * @return  self
     */ 
    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }
}
