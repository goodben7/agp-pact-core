<?php

namespace App\Dto\Company;

use App\Entity\GeneralParameter;

class CompanyUpdateDTO
{
    public ?string $name = null;
    public ?GeneralParameter $type = null;
    public ?string $contactEmail = null;
    public ?string $contactPhone = null;
    public ?bool $active = true;
    public ?array $roadAxes = [];
    public ?bool $canProcessSensitiveComplaint = null;
}
