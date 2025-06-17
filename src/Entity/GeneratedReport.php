<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Report\RequestReportDto;
use App\Repository\GeneratedReportRepository;
use App\State\Report\RequestReportProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GeneratedReportRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['generated_report:list']],
        ),
        new Get(
            normalizationContext: ['groups' => ['generated_report:get']],
        ),
        new Post(
            uriTemplate: '/reports/request',
            input: RequestReportDto::class,
            processor: RequestReportProcessor::class
        )
    ],
    security: "is_granted('ROLE_VIEW_GENERATED_REPORTS')",
)]
class GeneratedReport
{
    const ID_PREFIX = "GR";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?ReportTemplate $template = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?User $requestedBy = null;

    #[ORM\Column]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?\DateTimeImmutable $requestedAt = null;

    #[ORM\Column(length: 50)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?string $filePath = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?string $errorMessage = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?array $filtersApplied = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['generated_report:list', 'generated_report:get'])]
    private ?string $fileName = null;

    public function __construct()
    {
        if (is_null($this->requestedAt))
            $this->requestedAt = new \DateTimeImmutable();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTemplate(): ?ReportTemplate
    {
        return $this->template;
    }

    public function setTemplate(?ReportTemplate $template): static
    {
        $this->template = $template;

        return $this;
    }

    public function getRequestedBy(): ?User
    {
        return $this->requestedBy;
    }

    public function setRequestedBy(?User $requestedBy): static
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(\DateTimeImmutable $requestedAt): static
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): static
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getFiltersApplied(): ?array
    {
        return $this->filtersApplied;
    }

    public function setFiltersApplied(?array $filtersApplied): static
    {
        $this->filtersApplied = $filtersApplied;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }
}
