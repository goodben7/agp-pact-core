<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FaqRepository;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => 'faq:get'],
    operations:[
        new Get(
            security: 'is_granted("ROLE_FAQ_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_FAQ_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_FAQ_CREATE")',
            denormalizationContext: ['groups' => 'faq:post',],
            processor: PersistProcessor::class,
        ),
        new Patch(
            security: 'is_granted("ROLE_FAQ_UPDATE")',
            denormalizationContext: ['groups' => 'faq:patch',],
            processor: PersistProcessor::class,
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'sortOrder' => 'exact',
    'active' => 'exact'
])]
class Faq
{
    public const ID_PREFIX = "FA";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['faq:get'])]
    private ?string $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['faq:get', 'faq:post', 'faq:patch'])]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['faq:get', 'faq:post', 'faq:patch'])]
    private ?string $answer = null;

    #[ORM\Column]
    #[Groups(['faq:get', 'faq:post', 'faq:patch'])]
    private ?int $sortOrder = null;

    #[ORM\Column]
     #[Groups(['faq:get', 'faq:post', 'faq:patch'])]
    private ?bool $active = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get the value of active
     */ 
    public function getActive(): bool|null
    {
        return $this->active;
    }

    /**
     * Set the value of active
     *
     * @return  self
     */ 
    public function setActive($active): static
    {
        $this->active = $active;

        return $this;
    }
}
