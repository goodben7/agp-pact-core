<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PrejudiceConsequenceRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrejudiceConsequenceRepository::class)]
class PrejudiceConsequence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['prejudice:get'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'consequences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Prejudice $prejudice = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prejudice:get'])]
    private ?GeneralParameter $consequenceType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrejudice(): ?Prejudice
    {
        return $this->prejudice;
    }

    public function setPrejudice(?Prejudice $prejudice): static
    {
        $this->prejudice = $prejudice;

        return $this;
    }

    public function getConsequenceType(): ?GeneralParameter
    {
        return $this->consequenceType;
    }

    public function setConsequenceType(?GeneralParameter $consequenceType): static
    {
        $this->consequenceType = $consequenceType;

        return $this;
    }
}
