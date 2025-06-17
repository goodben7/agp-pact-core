<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Repository\ReportTemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ReportTemplateRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => 'report_template:list'],
            security: 'is_granted("ROLE_REPORT_TEMPLATE_LIST")',
            provider: CollectionProvider::class
        ),
        new Get(
            security: 'is_granted("ROLE_REPORT_TEMPLATE_DETAILS")',
            provider: ItemProvider::class
        ),
        new Post(
            denormalizationContext: ['groups' => 'report_template:post'],
            security: 'is_granted("ROLE_REPORT_TEMPLATE_CREATE")',
            processor: PersistProcessor::class,
        ),
        new Patch(
            denormalizationContext: ['groups' => 'report_template:patch'],
            security: 'is_granted("ROLE_REPORT_TEMPLATE_UPDATE")',
            processor: PersistProcessor::class,
        )
    ],
    normalizationContext: ['groups' => 'report_template:get']
)]
class ReportTemplate
{
    const ID_PREFIX = "RT";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['report_template:get', 'report_template:list', 'generated_report:list', 'generated_report:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post', 'generated_report:list', 'generated_report:get'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?GeneralParameter $reportType = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?GeneralParameter $format = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?string $templatePathOrContent = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?array $availableFilters = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?array $defaultFilterValues = null;

    #[ORM\Column]
    #[Groups(['report_template:get', 'report_template:list', 'report_template:post'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['report_template:get', 'report_template:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['report_template:get', 'report_template:list'])]
    private ?\DateTimeImmutable $updatedAt = null;


    public function __construct()
    {
        if (is_null($this->createdAt))
            $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReportType(): ?GeneralParameter
    {
        return $this->reportType;
    }

    public function setReportType(?GeneralParameter $reportType): static
    {
        $this->reportType = $reportType;

        return $this;
    }

    public function getFormat(): ?GeneralParameter
    {
        return $this->format;
    }

    public function setFormat(?GeneralParameter $format): static
    {
        $this->format = $format;

        return $this;
    }

    public function getTemplatePathOrContent(): ?string
    {
        return $this->templatePathOrContent;
    }

    public function setTemplatePathOrContent(?string $templatePathOrContent): static
    {
        $this->templatePathOrContent = $templatePathOrContent;

        return $this;
    }

    public function getAvailableFilters(): ?array
    {
        return $this->availableFilters;
    }

    public function setAvailableFilters(?array $availableFilters): static
    {
        $this->availableFilters = $availableFilters;

        return $this;
    }

    public function getDefaultFilterValues(): ?array
    {
        return $this->defaultFilterValues;
    }

    public function setDefaultFilterValues(?array $defaultFilterValues): static
    {
        $this->defaultFilterValues = $defaultFilterValues;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
