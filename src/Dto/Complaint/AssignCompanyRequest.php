<?php

namespace App\Dto\Complaint;

use App\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;

class AssignCompanyRequest
{
    #[Assert\NotBlank]
    public ?Company $company = null;

    public ?string $comments = null;
}