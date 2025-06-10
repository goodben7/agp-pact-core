<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\ComplainantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComplainantRepository::class)]
class Complainant
{
    const ID_PREFIX = "CN";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\Column(length: 120)]
    private ?string $lastName = null;

    #[ORM\Column(length: 120)]
    private ?string $firstName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 14)]
    private ?string $contactPhone = null;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $personType = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $province = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $territory = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $commune = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $quartier = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
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

    public function getPersonType(): ?GeneralParameter
    {
        return $this->personType;
    }

    public function setPersonType(?GeneralParameter $personType): static
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
