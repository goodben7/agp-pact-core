<?php

namespace App\Dto\Victim;

use App\Entity\GeneralParameter;

class VictimCreateDTO
{
    public ?string $complaintId = null;

    public ?string $lastName = null;

    public ?string $middleName = null;

    public ?string $firstName = null;

    public ?GeneralParameter $gender = null;

    public ?int $age = null;

    public ?GeneralParameter $vulnerabilityDegree = null;

    public ?GeneralParameter $familyRelationship = null;

    public ?string $victimDescription = null;
    
    public ?GeneralParameter $relationshipProject = null;
}
