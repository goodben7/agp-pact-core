<?php

namespace App\Entity;

use App\Dto\CreateTasktDto;
use App\Dto\CreateTaskstDto;
use App\Model\TaskInterface;
use ApiPlatform\Metadata\Get;
use App\Doctrine\IdGenerator;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TaskRepository;
use App\State\CreateTaskProcessor;
use ApiPlatform\Metadata\ApiFilter;
use App\Dto\CreateTasktDocumentDto;
use App\State\CreateTasksProcessor;
use App\Dto\CreateTaskstDocumentDto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\CreateTaskDocumentProcessor;
use App\State\CreateTasksDocumentProcessor;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)] 
#[ApiResource(
    normalizationContext: ['groups' => 'task:get'],
    operations: [
        new Get(
            security: 'is_granted("ROLE_TASK_DETAILS")',
            provider: ItemProvider::class
        ),
        new GetCollection(
            security: 'is_granted("ROLE_TASK_LIST")',
            provider: CollectionProvider::class
        ),
        new Post(
            security: 'is_granted("ROLE_TASK_CREATE")',
            input: CreateTasktDto::class,
            processor: CreateTaskProcessor::class,
        ),
        new Post(
            uriTemplate: "tasks/load",
            security: 'is_granted("ROLE_TASK_CREATE")',
            input: CreateTaskstDto::class,
            processor: CreateTasksProcessor::class,
        ),
        new Post(
            uriTemplate: "tasks/documents",
            security: 'is_granted("ROLE_TASK_CREATE")',
            input: CreateTasktDocumentDto::class,
            processor: CreateTaskDocumentProcessor::class,
        ),
        new Post(
            uriTemplate: "tasks/documents/load",
            security: 'is_granted("ROLE_TASK_CREATE")',
            input: CreateTaskstDocumentDto::class,
            processor: CreateTasksDocumentProcessor::class,
        )
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'status' => 'exact',
    'type' => 'exact',
    'method' => 'exact',
    'createdBy' => 'exact',
    'updatedBy' => 'exact',
    'synchronizedBy' => 'exact',
    'externalReferenceId' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['createdAt', 'updatedAt', 'synchronizedAt'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt', 'updatedAt', 'synchronizedAt'])]
class Task implements TaskInterface
{
    public const ID_PREFIX = "TA";

    public const STATUS_IDLE = 'I';
    public const STATUS_INPROGRESS = 'P';
    public const STATUS_TERMINATED = 'T';
    public const STATUS_FAILED = 'F';

    public const METHOD_CREATE = 'C';
    public const METHOD_UPDATE = 'U'; 

    public const TYPE_PAR ='PAR';
    public const TYPE_COMPLAINT ='COMPLAINT';
    public const TYPE_DOCUMENT = 'DOCUMENT';

    #[ORM\Id]
    #[ORM\GeneratedValue( strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(IdGenerator::class)]
    #[ORM\Column(length: 16)]
    #[Groups(groups: ['task:get'])]
    private ?string $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::TYPE_PAR, self::TYPE_DOCUMENT, self::TYPE_COMPLAINT])]
    #[Groups(groups: ['task:get'])]
    private ?string $type = null;

    #[ORM\Column(length: 15)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [self::METHOD_CREATE, self::METHOD_UPDATE])]
    #[Groups(groups: ['task:get'])]
    private ?string $method = null;

    #[ORM\Column(length: 1)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(groups: ['task:get'])]
    private ?string $status = self::STATUS_IDLE;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    #[Groups(groups: ['task:get'])]
    private array $data = [];

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(groups: ['task:get'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Groups(groups: ['task:get'])]
    private ?string $createdBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $updatedBy = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $synchronizedBy = null;

    #[ORM\Column(nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?\DateTimeImmutable $synchronizedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $message = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data5 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $data6 = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(groups: ['task:get'])]
    private ?string $externalReferenceId = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

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

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): static
    {
        $this->data = $data;

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

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getSynchronizedBy(): ?string
    {
        return $this->synchronizedBy;
    }

    public function setSynchronizedBy(?string $synchronizedBy): static
    {
        $this->synchronizedBy = $synchronizedBy;

        return $this;
    }

    public function getSynchronizedAt(): ?\DateTimeImmutable
    {
        return $this->synchronizedAt;
    }

    public function setSynchronizedAt(?\DateTimeImmutable $synchronizedAt): static
    {
        $this->synchronizedAt = $synchronizedAt;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getData1(): ?string
    {
        return $this->data1;
    }

    public function setData1(?string $data1): static
    {
        $this->data1 = $data1;

        return $this;
    }

    public function getData2(): ?string
    {
        return $this->data2;
    }

    public function setData2(?string $data2): static
    {
        $this->data2 = $data2;

        return $this;
    }

    public function getData3(): ?string
    {
        return $this->data3;
    }

    public function setData3(?string $data3): static
    {
        $this->data3 = $data3;

        return $this;
    }

    public function getData4(): ?string
    {
        return $this->data4;
    }

    public function setData4(?string $data4): static
    {
        $this->data4 = $data4;

        return $this;
    }

    public function getData5(): ?string
    {
        return $this->data5;
    }

    public function setData5(?string $data5): static
    {
        $this->data5 = $data5;

        return $this;
    }

    public function getData6(): ?string
    {
        return $this->data6;
    }

    public function setData6(?string $data6): static
    {
        $this->data6 = $data6;

        return $this;
    }

    public function getDataValue(string $key, mixed $defaultValue = null): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $defaultValue;
    }

    public function getExternalReferenceId(): ?string
    {
        return $this->externalReferenceId;
    }

    public function setExternalReferenceId(?string $externalReferenceId): static
    {
        $this->externalReferenceId = $externalReferenceId;

        return $this;
    }
}
