<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Doctrine\IdGenerator;
use App\Dto\Complaint\AttachedFileDto;
use App\Repository\AttachedFileRepository;
use App\State\Complaint\UploadAttachedFileProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Attribute\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AttachedFileRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            inputFormats: [
                'json' => ['application/json', 'application/merge-patch+json'],
                'multipart' => ['multipart/form-data'],
            ],
            input: AttachedFileDto::class,
            processor: UploadAttachedFileProcessor::class
        )
    ],
    normalizationContext: ['groups' => 'attached_file:get']
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact',
        'complaint.id' => 'exact',
        'fileType.code' => 'exact',
        'fileType.category' => 'exact',
        'workflowStep.id' => 'exact',
        'uploadedBy.id' => 'exact',
    ]
)]
class AttachedFile
{
    const ID_PREFIX = "AF";

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(['attached_file:get'])]
    private ?string $id = null;

    #[ORM\ManyToOne(inversedBy: 'attachedFiles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attached_file:get'])]
    private ?Complaint $complaint = null;

    #[ORM\Column(length: 255)]
    #[Groups(['attached_file:get'])]
    private ?string $fileName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['attached_file:get'])]
    private ?string $filePath = null;

    #[Vich\UploadableField(mapping: 'attached_files', fileNameProperty: 'filePath', size: 'fileSize', mimeType: 'mimeType', originalName: 'fileName')]
    private ?File $file = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attached_file:get'])]
    private ?GeneralParameter $fileType = null;

    #[ORM\Column]
    #[Groups(['attached_file:get'])]
    private ?int $fileSize = null;

    #[ORM\Column(length: 255)]
    #[Groups(['attached_file:get'])]
    private ?string $mimeType = null;

    #[ORM\ManyToOne]
    #[Groups(['attached_file:get'])]
    private ?WorkflowStep $workflowStep = null;

    #[ORM\Column]
    #[Groups(['attached_file:get'])]
    private ?\DateTimeImmutable $uploadedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['attached_file:get'])]
    private ?User $uploadedBy = null;

    public function __construct()
    {
        $this->uploadedAt = new \DateTimeImmutable();
    }

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

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFileType(): ?GeneralParameter
    {
        return $this->fileType;
    }

    public function setFileType(?GeneralParameter $fileType): static
    {
        $this->fileType = $fileType;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getWorkflowStep(): ?WorkflowStep
    {
        return $this->workflowStep;
    }

    public function setWorkflowStep(?WorkflowStep $workflowStep): static
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeImmutable $uploadedAt): static
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): static
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): void
    {
        $this->file = $file;
    }
}
