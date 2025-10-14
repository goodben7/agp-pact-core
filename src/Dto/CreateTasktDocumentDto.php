<?php

namespace App\Dto;

use App\Entity\Task;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTasktDocumentDto
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [Task::TYPE_PAR, Task::TYPE_DOCUMENT, Task::TYPE_COMPLAINT])]
    public ?string $type = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: [Task::METHOD_CREATE, Task::METHOD_UPDATE])]
    public ?string $method = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(max: 16)]
    public ?string $createdBy = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $externalReferenceId = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?\DateTimeImmutable $createdAt = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public string $file;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $documentType = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $documentRefNumber = null;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    public ?string $agentId = null;

    public array $data = [];

    public ?string $data5 = null;

    public ?string $data6 = null;
}