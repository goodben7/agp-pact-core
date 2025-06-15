<?php

namespace App\Dto\Complaint;

use App\Entity\GeneralParameter;

class ComplaintConsequenceCreateDTO
{
    public ?string $complaintId = null;

    public ?GeneralParameter $consequenceType = null;

    public ?GeneralParameter $severity = null;

    public ?float $estimatedCost = null;

    public ?string $impactDescription = null;

    public ?float $affectedQuantity = null;

    public ?GeneralParameter $affectedUnit = null;

    public ?GeneralParameter $affectedAssetType = null;
}
