<?php

namespace App\Dto\Complaint;

class ComplaintConsequenceCreateDTO
{
    public ?string $complaintId = null;
    public ?string $consequenceType = null;
    public ?string $severity = null;
    public ?float $estimatedCost = null;
    public ?string $impactDescription = null;
    public ?float $affectedQuantity = null;
    public ?string $affectedUnitId = null;
    public ?string $affectedAssetType = null;
}
