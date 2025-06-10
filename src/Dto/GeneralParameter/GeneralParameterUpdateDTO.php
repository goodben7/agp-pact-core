<?php

namespace App\Dto\GeneralParameter;

class GeneralParameterUpdateDTO
{
    public ?string $value = null;
    public ?string $code = null;
    public ?string $description = null;
    public ?bool $isActive = null;
    public ?int $displayOrder = null;
}

