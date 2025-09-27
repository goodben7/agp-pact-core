<?php

namespace App\Dto;

use App\Entity\Par;

use Symfony\Component\Validator\Constraints as Assert;

class ValidateParDto
{
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public Par $par;

}