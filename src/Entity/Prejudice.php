<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PrejudiceRepository;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: PrejudiceRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'prejudice:get'],
    operations:[
        new Get(
            security: 'is_granted("ROLE_PREJUDICE_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_PREJUDICE_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_PREJUDICE_CREATE")',
            denormalizationContext: ['groups' => 'prejudice:post',],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_PREJUDICE_UPDATE")',
            denormalizationContext: ['groups' => 'prejudice:patch',],
            processor: PersistProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'label' => 'ipartial',
    'category.code' => 'exact',
    'category.category' => 'exact',
    'complaintType.code' => 'exact',
    'complaintType.category' => 'exact',
    'active' => 'exact'
])]
class Prejudice
{
    public const ID_PREFIX = "PJ";

    #[ORM\Id]
    #[ORM\GeneratedValue( strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)] 
    #[Groups(['prejudice:get'])]  
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['prejudice:get', 'prejudice:post', 'prejudice:patch'])]  
    private ?string $label = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prejudice:get', 'prejudice:post', 'prejudice:patch'])]  
    private ?GeneralParameter $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['prejudice:get', 'prejudice:post', 'prejudice:patch'])]  
    private ?GeneralParameter $complaintType = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['prejudice:get', 'prejudice:post', 'prejudice:patch'])]  
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['prejudice:get', 'prejudice:post', 'prejudice:patch'])]  
    private ?bool $active = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getCategory(): ?GeneralParameter
    {
        return $this->category;
    }

    public function setCategory(?GeneralParameter $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getComplaintType(): ?GeneralParameter
    {
        return $this->complaintType;
    }

    public function setComplaintType(?GeneralParameter $complaintType): static
    {
        $this->complaintType = $complaintType;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
