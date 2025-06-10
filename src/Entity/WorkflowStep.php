<?php

namespace App\Entity;

use App\Doctrine\IdGenerator;
use App\Repository\WorkflowStepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowStepRepository::class)]
class WorkflowStep
{
    const ID_PREFIX = "WS";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column]
    private ?bool $isInitial = null;

    #[ORM\Column]
    private ?bool $isFinal = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(nullable: true)]
    private ?int $expectedDuration = null;

    #[ORM\ManyToOne]
    private ?GeneralParameter $durationUnit = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isInitial(): ?bool
    {
        return $this->isInitial;
    }

    public function setIsInitial(bool $isInitial): static
    {
        $this->isInitial = $isInitial;

        return $this;
    }

    public function isFinal(): ?bool
    {
        return $this->isFinal;
    }

    public function setIsFinal(bool $isFinal): static
    {
        $this->isFinal = $isFinal;

        return $this;
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

    public function getExpectedDuration(): ?int
    {
        return $this->expectedDuration;
    }

    public function setExpectedDuration(?int $expectedDuration): static
    {
        $this->expectedDuration = $expectedDuration;

        return $this;
    }

    public function getDurationUnit(): ?GeneralParameter
    {
        return $this->durationUnit;
    }

    public function setDurationUnit(?GeneralParameter $durationUnit): static
    {
        $this->durationUnit = $durationUnit;

        return $this;
    }
}
