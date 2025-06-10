<?php

namespace App\Dto\Company;

class CompanyCreateDTO
{
    public ?string $name = null;
    public ?string $type = null;
    public ?string $contactEmail = null;
    public ?string $contactPhone = null;
    public ?bool $isActive = true;
}
