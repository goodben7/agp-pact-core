<?php

namespace App\Dto\GeneralParameter;

class GeneralParameterCreateDTO
{
    public ?string $category = null;
    public ?string $value = null;
    public ?string $code = null;
    public ?string $description = null;
    public ?bool $isActive = true;
    public ?int $displayOrder = null;
}

