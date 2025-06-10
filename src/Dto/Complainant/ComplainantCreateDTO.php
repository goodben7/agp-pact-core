<?php

namespace App\Dto\Complainant;

class ComplainantCreateDTO
{
    public ?string $lastName = null;
    public ?string $middleName = null;
    public ?string $firstName = null;
    public ?string $contactPhone = null;
    public ?string $contactEmail = null;
    public ?string $personType = null;
    public ?string $address = null;
    public ?string $provinceId = null;
    public ?string $territoryId = null;
    public ?string $communeId = null;
    public ?string $quartierId = null;
    public ?string $cityId = null;
    public ?string $villageId = null;
}
