<?php

namespace App\Dto\Complaint;

class ComplaintCreateDTO
{
    public ?string $complaintType = null;
    public ?string $incidentDate = null;
    public ?string $incidentCause = null;
    public ?string $description = null;
    public ?string $roadAxisId = null;
    public ?string $locationDetail = null;
    public ?string $locationId = null;
    public ?float $latitude = null;
    public ?float $longitude = null;
    public ?string $complainantId = null;
    public ?string $assignedToId = null;
    public ?string $involvedCompanyId = null;
}
