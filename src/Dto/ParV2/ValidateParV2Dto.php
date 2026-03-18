<?php

namespace App\Dto\ParV2;

use App\Entity\ParV2;
use Symfony\Component\Validator\Constraints as Assert;

final class ValidateParV2Dto
{
    #[Assert\NotNull()]
    #[Assert\NotBlank()]
    public ParV2 $par_v2;

}
