<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskstDto
{
    /** @var CreateTasktDto[] $tasks */
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public array $tasks = [];
}