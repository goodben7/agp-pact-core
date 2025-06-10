<?php

namespace App\Dto\Complaint;

class AffectedSpeciesCreateDTO
{
    public ?string $complaintId = null;
    public ?string $speciesType = null;
    public ?string $speciesNature = null;
    public ?float $affectedQuantity = null;
    public ?string $affectedUnitId = null;
    public ?string $description = null;
    public ?string $assetType = null;
}
