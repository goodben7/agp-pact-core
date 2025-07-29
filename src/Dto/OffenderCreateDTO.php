<?php

namespace App\Dto;

use App\Entity\GeneralParameter;

class OffenderCreateDTO
{
    public ?string $complaintId = null;

    public ?string $lastName = null;

    public ?string $middleName = null;

    public ?string $firstName = null;

    public ?GeneralParameter $gender = null;

    public ?string $description = null;
}
