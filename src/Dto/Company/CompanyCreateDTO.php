<?php

namespace App\Dto\Company;

use App\Entity\GeneralParameter;
use App\Entity\Location;

class CompanyCreateDTO
{
    public ?string $name = null;

    public ?GeneralParameter $type = null;

    public ?string $contactEmail = null;

    public ?string $contactPhone = null;

    public ?bool $active = true;

    public ?Location $location= null;

    public ?array $roadAxes = [];

    public ?bool $canProcessSensitiveComplaint = null;
}
