<?php

namespace App\Dto\Complaint;

use App\Entity\Complainant;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\User;

class ComplaintCreateDTO
{
    public ?string $complaintType = null;

    public ?string $incidentDate = null;

    public ?string $incidentCause = null;

    public ?string $description = null;

    public ?RoadAxis $roadAxis = null;

    public ?string $locationDetail = null;

    public ?Location $location = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?Complainant $complainant = null;

    public ?User $assignedTo = null;

    public ?string $involvedCompanyId = null;
}
