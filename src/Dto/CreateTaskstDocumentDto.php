<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskstDocumentDto
{
    /** @var CreateTasktDocumentDto[] $tasks */
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public array $tasks = [];
}