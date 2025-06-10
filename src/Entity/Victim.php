<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\VictimRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VictimRepository::class)]
class Victim
{
    const ID_PREFIX = "VC";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'victims')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Complaint $complaint = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;

    #[ORM\ManyToOne]
    private ?GeneralParameter $gender = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GeneralParameter $vulnerabilityDegree = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $victimDescription = null;

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
}
