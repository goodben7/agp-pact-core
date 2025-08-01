<?php

namespace App\Dto;


use App\Entity\GeneralParameter;

class PrejudiceUpdateDTO
{
    public ?string $label = null;

    public ?string $description = null;

    public ?GeneralParameter $assetType = null;

    public ?bool $isSensible = null;

    public ?bool $active = null;
}
