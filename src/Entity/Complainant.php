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
use ApiPlatform\Metadata\GetCollection;
use App\Repository\ComplainantRepository;
use App\Dto\Complainant\ComplainantCreateDTO;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use App\State\Complainant\CreateComplainantProcessor;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: ComplainantRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER', fields: ['userId'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_CONTACT_PHONE', fields: ['contactPhone'])]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['complainant:list']],
            security: "is_granted('ROLE_COMPLAINANT_LIST')"
        ),
        new Get(
            normalizationContext: ['groups' => ['complainant:get']],
            security: "is_granted('ROLE_COMPLAINANT_DETAILS')"
        ),
        new Post(
            input: ComplainantCreateDTO::class,
            processor: CreateComplainantProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => 'complainant:patch'],
            security: "is_granted('ROLE_COMPLAINANT_UPDATE')",
            processor: PersistProcessor::class,
        )
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'lastName' => 'ipartial',
        'firstName' => 'ipartial',
        'middleName' => 'ipartial',
        'displayName' => 'ipartial',
        'contactPhone' => 'exact',
        'contactEmail' => 'exact',
        'personType.code' => 'exact',
        'province.code' => 'exact',
        'territory.code' => 'exact',
        'commune.code' => 'exact',
        'quartier.code' => 'exact',
        'city.code' => 'exact',
        'village.code' => 'exact',
        'secteur.code' => 'exact',
        'userId' => 'exact',
        'organizationStatus.code' => 'exact',
        'legalPersonality.code' => 'exact',
    ]
)]
class Complainant
{
    public const ID_PREFIX = "CN";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $id = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $middleName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $displayName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $contactEmail = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?GeneralParameter $personType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list', 'complainant:patch'])]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $province = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $territory = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $commune = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $quartier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $village = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?Location $secteur = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(groups: ['complainant:get', 'complainant:list'])]
    private ?string $userId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?GeneralParameter $organizationStatus = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complainant:patch'])]
    private ?GeneralParameter $legalPersonality = null;

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

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getProvince(): ?Location
    {
        return $this->province;
    }

    public function setProvince(?Location $province): static
    {
        $this->province = $province;

        return $this;
    }

    public function getTerritory(): ?Location
    {
        return $this->territory;
    }

    public function setTerritory(?Location $territory): static
    {
        $this->territory = $territory;

        return $this;
    }

    public function getCommune(): ?Location
    {
        return $this->commune;
    }

    public function setCommune(?Location $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getQuartier(): ?Location
    {
        return $this->quartier;
    }

    public function setQuartier(?Location $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getCity(): ?Location
    {
        return $this->city;
    }

    public function setCity(?Location $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getVillage(): ?Location
    {
        return $this->village;
    }

    public function setVillage(?Location $village): static
    {
        $this->village = $village;

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
    public function setUserId($userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function buildFullName()
    {
        return $this->displayName = $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName;
    }

    /**
     * Get the value of displayName
     */
    public function getDisplayName(): string|null
    {
        return $this->displayName;
    }

    /**
     * Set the value of displayName
     *
     * @return  self
     */
    public function setDisplayName($displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get the value of secteur
     */
    public function getSecteur(): Location|null
    {
        return $this->secteur;
    }

    /**
     * Set the value of secteur
     *
     * @return  self
     */
    public function setSecteur($secteur): static
    {
        $this->secteur = $secteur;

        return $this;
    }

    /**
     * Get the value of personType
     */
    public function getPersonType(): GeneralParameter|null
    {
        return $this->personType;
    }

    /**
     * Set the value of personType
     *
     * @return  self
     */
    public function setPersonType(?GeneralParameter $personType)
    {
        $this->personType = $personType;

        return $this;
    }

    /**
     * Get the value of organizationStatus
     */
    public function getOrganizationStatus(): GeneralParameter|null
    {
        return $this->organizationStatus;
    }

    /**
     * Set the value of organizationStatus
     *
     * @return  self
     */
    public function setOrganizationStatus(?GeneralParameter $organizationStatus): static
    {
        $this->organizationStatus = $organizationStatus;

        return $this;
    }

    /**
     * Get the value of legalPersonality
     */
    public function getLegalPersonality(): GeneralParameter|null
    {
        return $this->legalPersonality;
    }

    /**
     * Set the value of legalPersonality
     *
     * @return  self
     */
    public function setLegalPersonality(?GeneralParameter $legalPersonality): static
    {
        $this->legalPersonality = $legalPersonality;

        return $this;
    }
}
