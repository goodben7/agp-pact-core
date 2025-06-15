<?php

namespace App\Dto\Complaint;

use App\Entity\GeneralParameter;

class AffectedSpeciesCreateDTO
{
    public ?string $complaintId = null;

    public ?GeneralParameter $speciesType = null;

    public ?string $speciesNature = null;

    public ?float $affectedQuantity = null;

    public ?GeneralParameter $affectedUnit = null;

    public ?string $description = null;

    public ?GeneralParameter $assetType = null;
}
