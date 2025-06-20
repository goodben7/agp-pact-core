<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\VictimRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: VictimRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            security: 'is_granted("ROLE_VICTIM_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_VICTIM_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'victim:post',],
            security: 'is_granted("ROLE_VICTIM_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'victim:patch',],
            security: 'is_granted("ROLE_VICTIM_UPDATE")',
            processor: PersistProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => 'victim:get']
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
        'vulnerabilityDegree.code' => 'exact',
        'vulnerabilityDegree.category' => 'exact',
        'complaint' => 'exact',
    ]
)]
class Victim
{
    public const ID_PREFIX = "VC";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['victim:get', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'victims')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['victim:get', 'victim:post'])]
    private ?Complaint $complaint = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['victim:get', 'complaint:get', 'complaint:list'])]
    private ?string $fullName = null;

    #[ORM\ManyToOne]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $gender = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?int $age = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $vulnerabilityDegree = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?string $victimDescription = null;

    #[ORM\ManyToOne]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?GeneralParameter $familyRelationship = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['victim:get', 'victim:post', 'victim:patch', 'complaint:get', 'complaint:list'])]
    private ?\DateTimeImmutable $dateOfbirth = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function setFirstName(string $firstName): static
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getVulnerabilityDegree(): ?GeneralParameter
    {
        return $this->vulnerabilityDegree;
    }

    public function setVulnerabilityDegree(?GeneralParameter $vulnerabilityDegree): static
    {
        $this->vulnerabilityDegree = $vulnerabilityDegree;

        return $this;
    }

    public function getVictimDescription(): ?string
    {
        return $this->victimDescription;
    }

    public function setVictimDescription(string $victimDescription): static
    {
        $this->victimDescription = $victimDescription;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function buildFullName()
    {
        return $this->fullName = $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    public function getFamilyRelationship(): ?GeneralParameter
    {
        return $this->familyRelationship;
    }

    public function setFamilyRelationship(?GeneralParameter $familyRelationship): static
    {
        $this->familyRelationship = $familyRelationship;

        return $this;
    }

    /**
     * Get the value of dateOfbirth
     */
    public function getDateOfbirth(): \DateTimeImmutable|null
    {
        return $this->dateOfbirth;
    }

    /**
     * Set the value of dateOfbirth
     *
     * @return  self
     */
    public function setDateOfbirth($dateOfbirth): static
    {
        $this->dateOfbirth = $dateOfbirth;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
