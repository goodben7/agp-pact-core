<?php

namespace App\Dto\Victim;

class VictimUpdateDTO
{
    public ?string $lastName = null;
    public ?string $middleName = null;
    public ?string $firstName = null;
    public ?string $gender = null;
    public ?int $age = null;
    public ?string $vulnerabilityDegree = null;
    public ?string $victimDescription = null;
}
