<?php

namespace App\Dto;

use App\Entity\GeneralParameter;

class PrejudiceCreateDTO
{
    public ?string $label = null;

    public ?string $description = null;

    public ?GeneralParameter $assetType = null;

    public ?bool $active = null;
}
