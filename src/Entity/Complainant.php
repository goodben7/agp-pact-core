<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Complainant\ComplainantCreateDTO;
use App\Repository\ComplainantRepository;
use App\State\Complainant\CreateComplainantProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ComplainantRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['complainant:list']],
            security: "is_granted('ROLE_COMPLAINANT_LIST')"
        ),
        new Get(
            normalizationContext: ['groups' => ['complainant:get']],
            security: "is_granted('ROLE_COMPLAINANT_VIEW')"
        ),
        new Post(
            security: "is_granted('ROLE_COMPLAINANT_CREATE')",
            input: ComplainantCreateDTO::class,
            processor: CreateComplainantProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_COMPLAINANT_UPDATE')"
        )
    ]
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'lastName' => 'partial',
        'firstName' => 'partial',
        'middleName' => 'partial',
        'contactPhone' => 'exact',
        'contactEmail' => 'exact',
        'personType.code' => 'exact',
        'province.code' => 'exact',
        'territory.code' => 'exact',
        'commune.code' => 'exact',
        'quartier.code' => 'exact',
        'city.code' => 'exact',
        'village.code' => 'exact'
    ]
)]
class Complainant
{
    const ID_PREFIX = "CN";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $id = null;

    #[ORM\Column(length: 120)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 120)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $middleName = null;

    #[ORM\Column(length: 14)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 10)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $personType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['complainant:list', 'complainant:get', 'complaint:get', 'complaint:list'])]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $province = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $territory = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $commune = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $quartier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['complainant:list', 'complainant:get'])]
    private ?Location $village = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
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

    public function getPersonType(): ?string
    {
        return $this->personType;
    }

    public function setPersonType(?string $personType): static
    {
        $this->personType = $personType;

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
}
