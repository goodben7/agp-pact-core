<?php

namespace App\Dto\Complaint;

use App\Entity\GeneralParameter;
use App\Entity\Species;

class AffectedSpeciesCreateDTO
{
    public ?string $complaintId = null;

    public ?Species $speciesType = null;

    public ?string $speciesNature = null;

    public ?float $affectedQuantity = null;

    public ?GeneralParameter $affectedUnit = null;

    public ?string $description = null;

    public ?GeneralParameter $assetType = null;
}
