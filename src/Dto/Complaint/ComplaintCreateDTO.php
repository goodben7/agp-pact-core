<?php

namespace App\Dto\Complaint;

use App\Dto\Victim\VictimCreateDTO;
use App\Entity\Company;
use App\Entity\Complainant;
use App\Entity\GeneralParameter;
use App\Entity\Location;
use App\Entity\RoadAxis;
use App\Entity\User;

class ComplaintCreateDTO
{
    public ?GeneralParameter $complaintType = null;

    public ?\DateTimeImmutable $incidentDate = null;

    public ?GeneralParameter $incidentCause = null;

    public ?string $description = null;

    public ?RoadAxis $roadAxis = null;

    public ?string $locationDetail = null;

    public ?Location $location = null;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?Complainant $complainant = null;

    public ?User $assignedTo = null;

    public ?Company $involvedCompany = null;

    /** @var VictimCreateDTO[] $victims */
    public ?array $victims = null;

    /** @var AffectedSpeciesCreateDTO[] $affectedSpecies */
    public ?array $affectedSpecies = null;

    /** @var ComplaintConsequenceCreateDTO[] $complaintConsequences */
    public ?array $complaintConsequences = null;
}
